<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'description',
        'category_id',
        'unit_price',
        'cost_price',
        'stock_quantity',
        'minimum_stock',
        'maximum_stock',
        'unit',
        'is_active',
        'tax_rate',
        'image',
        'weight',
        'expiry_date'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'minimum_stock' => 'integer',
        'maximum_stock' => 'integer',
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'expiry_date' => 'date'
    ];

    protected $appends = ['image_url', 'profit_margin', 'stock_status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_products')
                    ->withPivot('supplier_sku', 'pack_quantity', 'pack_unit', 'pack_price', 'unit_price', 'minimum_order_quantity', 'lead_time_days', 'is_active')
                    ->withTimestamps();
    }

    public function supplierProducts()
    {
        return $this->hasMany(SupplierProduct::class);
    }

    /**
     * Get the product image URL.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::url($this->image);
        }
        return null;
    }

    /**
     * Get the profit margin percentage.
     */
    public function getProfitMarginAttribute()
    {
        if ($this->cost_price > 0) {
            return round((($this->unit_price - $this->cost_price) / $this->cost_price) * 100, 2);
        }
        return 0;
    }

    /**
     * Get the stock status.
     */
    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock_quantity <= $this->minimum_stock) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    /**
     * Check if product is low on stock.
     */
    public function isLowStock()
    {
        return $this->stock_quantity <= $this->minimum_stock;
    }

    /**
     * Check if product is out of stock.
     */
    public function isOutOfStock()
    {
        return $this->stock_quantity <= 0;
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Get the total value of current stock.
     */
    public function getStockValueAttribute()
    {
        return $this->stock_quantity * $this->cost_price;
    }

    /**
     * Get the total selling value of current stock.
     */
    public function getSellingValueAttribute()
    {
        return $this->stock_quantity * $this->unit_price;
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive products.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include in-stock products.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Scope a query to only include low stock products.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'minimum_stock')
                     ->where('stock_quantity', '>', 0);
    }

    /**
     * Scope a query to only include out of stock products.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    /**
     * Search products by name, SKU, or barcode.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('sku', 'LIKE', "%{$search}%")
                     ->orWhere('barcode', 'LIKE', "%{$search}%");
    }

    /**
     * Get the status badge color.
     */
    public function getStatusBadgeAttribute()
    {
        if (!$this->is_active) {
            return ['color' => 'gray', 'text' => 'Inactive'];
        }
        
        if ($this->isOutOfStock()) {
            return ['color' => 'red', 'text' => 'Out of Stock'];
        }
        
        if ($this->isLowStock()) {
            return ['color' => 'yellow', 'text' => 'Low Stock'];
        }
        
        return ['color' => 'green', 'text' => 'Active'];
    }

    /**
     * Delete the product image when product is deleted.
     */
    protected static function booted()
    {
        static::deleting(function ($product) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
        });
    }
}