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
            $query->whereRaw('stock_quantity <= minimum_stock')
                  ->where('stock_quantity', '>', 0);
        }
        
        return $query->orderBy('name')->paginate($perPage);
    }

    public function getLowStockProducts()
    {
        return Cache::remember('low_stock_products', 300, function() {
            return Product::where('stock_quantity', '<=', DB::raw('minimum_stock'))
                ->where('stock_quantity', '>', 0)
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
            
            if (empty($data['sku'])) {
                $data['sku'] = $this->generateSku($data['name']);
            }
            
            if (empty($data['barcode'])) {
                $data['barcode'] = $this->generateBarcode();
            }
            
            $product = Product::create($data);
            
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
            
            if ($type === 'remove' && $afterQuantity < 0) {
                throw new \Exception("Insufficient stock. Available: {$beforeQuantity}, Requested: {$quantity}");
            }
            
            $product->stock_quantity = $afterQuantity;
            $product->save();
            
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