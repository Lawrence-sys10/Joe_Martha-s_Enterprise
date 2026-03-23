<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'contact_person',
        'contact_person_phone',
        'tax_number',
        'opening_balance',
        'current_balance',
        'is_active',
        'notes',
        'payment_terms'
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    
    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }
    
    public function products()
    {
        return $this->belongsToMany(Product::class, 'supplier_products')
                    ->withPivot('supplier_sku', 'pack_quantity', 'pack_unit', 'pack_price', 'unit_price', 'minimum_order_quantity', 'lead_time_days', 'is_active')
                    ->withTimestamps();
    }
    
    public function supplierProducts()
    {
        return $this->hasMany(SupplierProduct::class);
    }
}