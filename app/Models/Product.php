<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function isLowStock()
    {
        return $this->stock_quantity <= $this->minimum_stock;
    }

    public function isOutOfStock()
    {
        return $this->stock_quantity <= 0;
    }
}