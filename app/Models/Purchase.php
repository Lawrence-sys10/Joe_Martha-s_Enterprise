<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'purchase_date',
        'subtotal',
        'tax',
        'total',
        'status',
        'notes',
        'user_id',
        'payment_status',
        'due_date'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'purchase_date' => 'date',
        'due_date' => 'date'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function getBalanceAttribute()
    {
        $paid = $this->payments()->sum('amount');
        return $this->total - $paid;
    }
    
    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount');
    }
    
    public function getPaymentPercentageAttribute()
    {
        if ($this->total <= 0) return 0;
        return ($this->paid_amount / $this->total) * 100;
    }
}