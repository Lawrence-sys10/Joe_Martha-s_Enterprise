<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier', 'user');
        
        if ($request->get('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        if ($request->get('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->get('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        if ($request->get('start_date')) {
            $query->whereDate('purchase_date', '>=', $request->start_date);
        }
        
        if ($request->get('end_date')) {
            $query->whereDate('purchase_date', '<=', $request->end_date);
        }
        
        $purchases = $query->orderBy('purchase_date', 'desc')->paginate(20);
        $suppliers = Supplier::where('is_active', true)->get();
        
        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        return redirect()->route('suppliers.index')
            ->with('info', 'Please select a supplier first to create a purchase order.');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0', // What you pay the supplier (tax included)
            'items.*.unit_price' => 'required|numeric|min:0', // What customers will pay
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            
            $invoiceNumber = $this->generateInvoiceNumber();
            
            // Calculate totals using COST PRICE (tax already included by supplier)
            $subtotal = 0;
            
            foreach ($request->items as $item) {
                $itemTotal = $item['quantity'] * $item['cost_price'];
                $subtotal += $itemTotal;
            }
            
            $total = $subtotal; // No tax added
            
            // Create purchase
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'invoice_number' => $invoiceNumber,
                'purchase_date' => $request->purchase_date,
                'due_date' => $request->due_date,
                'subtotal' => $subtotal,
                'tax' => 0, // No tax since supplier includes it
                'total' => $total,
                'status' => 'pending',
                'payment_status' => 'pending',
                'notes' => $request->notes,
                'user_id' => auth()->id(),
            ]);
            
            // Create purchase items and update product
            foreach ($request->items as $item) {
                $itemTotal = $item['quantity'] * $item['cost_price'];
                
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['cost_price'], // Purchase price from supplier (tax included)
                    'unit_price' => $item['unit_price'], // Selling price to customers
                    'total' => $itemTotal,
                    'expiry_date' => $item['expiry_date'] ?? null,
                ]);
                
                // Update product
                $product = Product::find($item['product_id']);
                $oldStock = $product->stock_quantity;
                $oldCostPrice = $product->cost_price;
                
                // Update stock quantity
                $product->stock_quantity += $item['quantity'];
                
                // Update cost price using weighted average (based on purchase cost, tax included)
                if ($product->stock_quantity > 0) {
                    $oldTotalCost = $oldCostPrice * $oldStock;
                    $newTotalCost = $oldTotalCost + ($item['cost_price'] * $item['quantity']);
                    $product->cost_price = $newTotalCost / $product->stock_quantity;
                }
                
                // Update unit price (selling price) - this is the NEW selling price
                $product->unit_price = $item['unit_price'];
                
                $product->save();
                
                // Record stock movement
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => StockMovement::TYPE_PURCHASE,
                    'quantity' => $item['quantity'],
                    'before_quantity' => $oldStock,
                    'after_quantity' => $product->stock_quantity,
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'notes' => "Purchase: Cost GHS {$item['cost_price']} (tax incl.) | Sell GHS {$item['unit_price']} | Qty: {$item['quantity']}",
                    'user_id' => auth()->id(),
                ]);
            }
            
            // Update supplier balance (what you owe the supplier) - based on COST PRICE total (tax included)
            $supplier = Supplier::find($request->supplier_id);
            $supplier->current_balance += $total;
            $supplier->save();
            
            DB::commit();
            
            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase order created successfully! Invoice: ' . $invoiceNumber);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create purchase: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product', 'payments.user']);
        
        $paidAmount = $purchase->payments->sum('amount');
        $balance = $purchase->total - $paidAmount;
        
        return view('purchases.show', compact('purchase', 'paidAmount', 'balance'));
    }

    public function edit(Purchase $purchase)
    {
        if ($purchase->status != 'pending') {
            return redirect()->route('purchases.index')
                ->with('error', 'Cannot edit completed or cancelled purchases.');
        }
        
        $suppliers = Supplier::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->status != 'pending') {
            return redirect()->route('purchases.index')
                ->with('error', 'Cannot update completed or cancelled purchases.');
        }
        
        return redirect()->route('purchases.index')
            ->with('info', 'Update functionality coming soon.');
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->status != 'pending') {
            return redirect()->back()
                ->with('error', 'Cannot delete completed or cancelled purchases.');
        }
        
        try {
            DB::beginTransaction();
            
            foreach ($purchase->items as $item) {
                $product = $item->product;
                $oldStock = $product->stock_quantity;
                $product->stock_quantity -= $item->quantity;
                $product->save();
                
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => StockMovement::TYPE_RETURN,
                    'quantity' => $item->quantity,
                    'before_quantity' => $oldStock,
                    'after_quantity' => $product->stock_quantity,
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'notes' => "Purchase order deleted #{$purchase->invoice_number}",
                    'user_id' => auth()->id(),
                ]);
            }
            
            $supplier = $purchase->supplier;
            $supplier->current_balance -= $purchase->total;
            $supplier->save();
            
            $purchase->delete();
            
            DB::commit();
            return redirect()->route('purchases.index')
                ->with('success', 'Purchase order deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete purchase: ' . $e->getMessage());
        }
    }

    public function complete(Purchase $purchase)
    {
        if ($purchase->status != 'pending') {
            return redirect()->back()
                ->with('error', 'Purchase already completed or cancelled.');
        }
        
        $purchase->status = 'completed';
        $purchase->save();
        
        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase marked as completed!');
    }

    public function recordPayment(Request $request, Purchase $purchase)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            DB::beginTransaction();
            
            $currentPaid = $purchase->payments()->sum('amount');
            $newPaid = $currentPaid + $request->amount;
            
            if ($request->amount > ($purchase->total - $currentPaid)) {
                return redirect()->back()->with('error', 'Payment amount cannot exceed remaining balance.');
            }
            
            $paymentNumber = 'PPMT-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            $payment = PurchasePayment::create([
                'purchase_id' => $purchase->id,
                'payment_number' => $paymentNumber,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'user_id' => auth()->id(),
            ]);
            
            if ($newPaid >= $purchase->total) {
                $purchase->payment_status = 'paid';
                $purchase->status = 'completed';
            } else {
                $purchase->payment_status = 'partial';
                $purchase->status = 'pending';
            }
            $purchase->save();
            
            $supplier = $purchase->supplier;
            $supplier->current_balance -= $request->amount;
            $supplier->save();
            
            DB::commit();
            
            $statusMessage = $newPaid >= $purchase->total ? 'fully paid' : 'partially paid';
            return redirect()->route('purchases.show', $purchase)
                ->with('success', "Payment of GHS " . number_format($request->amount, 2) . " recorded successfully! Purchase is now {$statusMessage}.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    public function printOrder(Purchase $purchase)
    {
        $purchase->load('supplier', 'items.product');
        return view('purchases.print', compact('purchase'));
    }

    private function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastPurchase = Purchase::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastPurchase ? intval(substr($lastPurchase->invoice_number, -4)) + 1 : 1;
        
        return "PO-{$year}{$month}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}