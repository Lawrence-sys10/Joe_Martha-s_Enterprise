<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseItem extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'cost_price',  // Add this
        'unit_price',
        'total',
        'expiry_date',
    ];
    
    protected $casts = [
        'cost_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'expiry_date' => 'date',
    ];
    
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}