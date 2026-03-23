# Step4-Services.ps1
# Run this script to create all service classes with business logic

Write-Host "Step 4: Creating Service Classes..." -ForegroundColor Green

# Create Services directory if not exists
New-Item -ItemType Directory -Force -Path "app\Services"

# Create ProductService
@'
<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    public function getAllProducts($perPage = 20, $filters = [])
    {
        $query = Product::with('category');
        
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('sku', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('barcode', 'LIKE', "%{$filters['search']}%");
            });
        }
        
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        if (!empty($filters['low_stock'])) {
            $query->whereRaw('stock_quantity <= minimum_stock');
        }
        
        return $query->orderBy('name')->paginate($perPage);
    }

    public function getLowStockProducts()
    {
        return Cache::remember('low_stock_products', 300, function() {
            return Product::where('stock_quantity', '<=', DB::raw('minimum_stock'))
                ->where('is_active', true)
                ->get();
        });
    }

    public function getOutOfStockProducts()
    {
        return Product::where('stock_quantity', '<=', 0)
            ->where('is_active', true)
            ->get();
    }

    public function getProductBySku($sku)
    {
        return Product::where('sku', $sku)->first();
    }

    public function getProductByBarcode($barcode)
    {
        return Product::where('barcode', $barcode)->first();
    }

    public function createProduct(array $data)
    {
        try {
            DB::beginTransaction();
            
            // Generate SKU if not provided
            if (empty($data['sku'])) {
                $data['sku'] = $this->generateSku($data['name']);
            }
            
            // Generate barcode if not provided
            if (empty($data['barcode'])) {
                $data['barcode'] = $this->generateBarcode();
            }
            
            $product = Product::create($data);
            
            // Create initial stock movement if quantity > 0
            if ($product->stock_quantity > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => StockMovement::TYPE_ADJUSTMENT,
                    'quantity' => $product->stock_quantity,
                    'before_quantity' => 0,
                    'after_quantity' => $product->stock_quantity,
                    'notes' => 'Initial stock setup',
                    'user_id' => auth()->id(),
                ]);
            }
            
            DB::commit();
            $this->clearProductCache();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create product: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateProduct(Product $product, array $data)
    {
        try {
            DB::beginTransaction();
            $product->update($data);
            DB::commit();
            $this->clearProductCache();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update product: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteProduct(Product $product)
    {
        try {
            DB::beginTransaction();
            $product->delete();
            DB::commit();
            $this->clearProductCache();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete product: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateStock(Product $product, int $quantity, string $type, string $notes = null)
    {
        try {
            DB::beginTransaction();
            
            $beforeQuantity = $product->stock_quantity;
            $afterQuantity = $type === 'add' ? $beforeQuantity + $quantity : $beforeQuantity - $quantity;
            
            // Validate stock for removal
            if ($type === 'remove' && $afterQuantity < 0) {
                throw new \Exception("Insufficient stock. Available: {$beforeQuantity}, Requested: {$quantity}");
            }
            
            // Update product stock
            $product->stock_quantity = $afterQuantity;
            $product->save();
            
            // Record stock movement
            StockMovement::create([
                'product_id' => $product->id,
                'type' => StockMovement::TYPE_ADJUSTMENT,
                'quantity' => $quantity,
                'before_quantity' => $beforeQuantity,
                'after_quantity' => $afterQuantity,
                'notes' => $notes,
                'user_id' => auth()->id(),
            ]);
            
            DB::commit();
            $this->clearProductCache();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update stock: ' . $e->getMessage());
            throw $e;
        }
    }

    public function searchProducts($searchTerm, $limit = 10)
    {
        return Product::where('name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('sku', 'LIKE', "%{$searchTerm}%")
            ->orWhere('barcode', 'LIKE', "%{$searchTerm}%")
            ->where('is_active', true)
            ->limit($limit)
            ->get();
    }

    private function generateSku($name)
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $name), 0, 3));
        $count = Product::where('sku', 'LIKE', "{$prefix}%")->count() + 1;
        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    private function generateBarcode()
    {
        return 'JM' . str_pad(mt_rand(1, 999999999), 9, '0', STR_PAD_LEFT);
    }
    
    private function clearProductCache()
    {
        Cache::forget('low_stock_products');
        Cache::forget('product_list');
    }
}
'@ | Out-File -FilePath "app\Services\ProductService.php" -Encoding UTF8 -NoNewline

# Create SalesService
@'
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
'@ | Out-File -FilePath "app\Services\SalesService.php" -Encoding UTF8 -NoNewline

Write-Host "Step 4 Complete: Service classes created!" -ForegroundColor Green
Write-Host ""
Write-Host "Created services:" -ForegroundColor Yellow
Write-Host "  - ProductService: Product CRUD, stock management, search" -ForegroundColor Green
Write-Host "  - SalesService: POS transactions, receipts, void sales" -ForegroundColor Green
Write-Host ""
Write-Host "Type 'next' to proceed to Step 5: Controllers" -ForegroundColor Yellow