<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'product_id',
        'supplier_sku',
        'pack_quantity',
        'pack_unit',
        'pack_price',
        'unit_price',
        'minimum_order_quantity',
        'lead_time_days',
        'is_active'
    ];

    protected $casts = [
        'pack_quantity' => 'integer',
        'pack_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'minimum_order_quantity' => 'integer',
        'lead_time_days' => 'integer',
        'is_active' => 'boolean'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getPricePerUnitAttribute()
    {
        if ($this->pack_quantity > 0 && $this->pack_price > 0) {
            return $this->pack_price / $this->pack_quantity;
        }
        return $this->unit_price;
    }
}