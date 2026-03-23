<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesService
{
    public function getAllSales($perPage = 20, $filters = [])
    {
        $query = Sale::with('customer', 'user', 'items');
        
        if (!empty($filters['start_date'])) {
            $query->whereDate('sale_date', '>=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $query->whereDate('sale_date', '<=', $filters['end_date']);
        }
        
        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }
        
        return $query->orderBy('sale_date', 'desc')->paginate($perPage);
    }

    public function getSale($id)
    {
        return Sale::with('customer', 'user', 'items.product', 'payments')->findOrFail($id);
    }

    public function createSale(array $data, array $items)
    {
        try {
            DB::beginTransaction();
            
            // Generate invoice number
            $data['invoice_number'] = $this->generateInvoiceNumber();
            $data['sale_date'] = now();
            $data['user_id'] = auth()->id();
            
            // Calculate totals
            $subtotal = 0;
            $tax = 0;
            $discount = $data['discount'] ?? 0;
            $validatedItems = [];
            
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock availability
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->stock_quantity}");
                }
                
                $itemTotal = $product->unit_price * $item['quantity'];
                $itemTax = $itemTotal * ($product->tax_rate / 100);
                
                $validatedItems[] = [
                    'product_id' => $product->id,
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->unit_price,
                    'discount' => $item['discount'] ?? 0,
                    'total' => $itemTotal,
                    'tax_amount' => $itemTax,
                ];
                
                $subtotal += $itemTotal;
                $tax += $itemTax;
            }
            
            $total = $subtotal + $tax - $discount;
            
            $data['subtotal'] = $subtotal;
            $data['tax'] = $tax;
            $data['total'] = $total;
            $data['paid_amount'] = $data['paid_amount'] ?? $total;
            $data['change_amount'] = $data['paid_amount'] - $total;
            $data['status'] = Sale::STATUS_COMPLETED;
            $data['payment_status'] = 'paid';
            
            // Create sale
            $sale = Sale::create($data);
            
            // Create sale items and update stock
            foreach ($validatedItems as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'],
                    'total' => $item['total'],
                    'tax_amount' => $item['tax_amount'],
                ]);
                
                // Update product stock
                $product = $item['product'];
                $beforeQuantity = $product->stock_quantity;
                $afterQuantity = $beforeQuantity - $item['quantity'];
                
                $product->stock_quantity = $afterQuantity;
                $product->save();
                
                // Record stock movement
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => StockMovement::TYPE_SALE,
                    'quantity' => $item['quantity'],
                    'before_quantity' => $beforeQuantity,
                    'after_quantity' => $afterQuantity,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'notes' => "Sale #{$sale->invoice_number}",
                    'user_id' => auth()->id(),
                ]);
            }
            
            // Create payment record
            Payment::create([
                'sale_id' => $sale->id,
                'amount' => $data['paid_amount'],
                'payment_method' => $data['payment_method'],
                'payment_date' => now(),
                'user_id' => auth()->id(),
            ]);
            
            // Create transaction record
            Transaction::create([
                'transaction_number' => 'TRX-' . $sale->invoice_number,
                'type' => Transaction::TYPE_SALE,
                'amount' => $total,
                'payment_method' => $data['payment_method'],
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'transaction_date' => now(),
                'notes' => "Sale transaction for invoice #{$sale->invoice_number}",
                'user_id' => auth()->id(),
            ]);
            
            DB::commit();
            return $sale;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create sale: ' . $e->getMessage());
            throw $e;
        }
    }

    public function generateReceipt(Sale $sale)
    {
        $pdf = Pdf::loadView('sales.receipt', compact('sale'));
        return $pdf->download("receipt-{$sale->invoice_number}.pdf");
    }

    public function getDailySales($date = null)
    {
        $date = $date ?? now()->toDateString();
        
        return Sale::with('items', 'user')
            ->whereDate('sale_date', $date)
            ->where('status', Sale::STATUS_COMPLETED)
            ->get();
    }

    public function getSalesSummary($startDate, $endDate)
    {
        return Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', Sale::STATUS_COMPLETED)
            ->select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('COUNT(*) as total_sales'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('SUM(tax) as total_tax'),
                DB::raw('SUM(discount) as total_discount'),
                DB::raw('AVG(total) as average_sale')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getSalesReport($startDate, $endDate)
    {
        return Sale::with('items.product', 'user', 'customer')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', Sale::STATUS_COMPLETED)
            ->orderBy('sale_date', 'desc')
            ->get();
    }

    public function voidSale(Sale $sale, $reason = null)
    {
        try {
            DB::beginTransaction();
            
            // Restore stock for voided sale
            foreach ($sale->items as $item) {
                $product = $item->product;
                $beforeQuantity = $product->stock_quantity;
                $afterQuantity = $beforeQuantity + $item->quantity;
                
                $product->stock_quantity = $afterQuantity;
                $product->save();
                
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => StockMovement::TYPE_RETURN,
                    'quantity' => $item->quantity,
                    'before_quantity' => $beforeQuantity,
                    'after_quantity' => $afterQuantity,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'notes' => "Void sale #{$sale->invoice_number}: {$reason}",
                    'user_id' => auth()->id(),
                ]);
            }
            
            $sale->status = Sale::STATUS_CANCELLED;
            $sale->notes = ($sale->notes ? $sale->notes . "\n" : '') . "Voided: {$reason}";
            $sale->save();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to void sale: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateInvoiceNumber()
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $lastSale = Sale::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastSale ? intval(substr($lastSale->invoice_number, -4)) + 1 : 1;
        
        return "INV-{$year}{$month}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}