<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    public function getStockValue()
    {
        return Product::where('is_active', true)
            ->select(DB::raw('SUM(stock_quantity * cost_price) as total_value'))
            ->first()
            ->total_value ?? 0;
    }

    public function getStockLevels()
    {
        return Product::where('is_active', true)
            ->select('name', 'sku', 'stock_quantity', 'minimum_stock', 'unit')
            ->orderBy('stock_quantity', 'asc')
            ->get();
    }

    public function processPurchase(Purchase $purchase)
    {
        try {
            DB::beginTransaction();
            
            foreach ($purchase->items as $item) {
                $product = $item->product;
                $beforeQuantity = $product->stock_quantity;
                $afterQuantity = $beforeQuantity + $item->quantity;
                
                // Update product stock
                $product->stock_quantity = $afterQuantity;
                
                // Update cost price (weighted average)
                $newTotalCost = ($product->cost_price * $beforeQuantity) + ($item->unit_price * $item->quantity);
                if ($afterQuantity > 0) {
                    $product->cost_price = $newTotalCost / $afterQuantity;
                }
                
                $product->save();
                
                // Record stock movement
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => StockMovement::TYPE_PURCHASE,
                    'quantity' => $item->quantity,
                    'before_quantity' => $beforeQuantity,
                    'after_quantity' => $afterQuantity,
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'notes' => "Purchase #{$purchase->invoice_number}",
                    'user_id' => auth()->id(),
                ]);
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process purchase: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getLowStockAlert()
    {
        return Product::where('stock_quantity', '<=', DB::raw('minimum_stock'))
            ->where('stock_quantity', '>', 0)
            ->where('is_active', true)
            ->get();
    }
}