<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_SALE = 'sale';
    const TYPE_PURCHASE = 'purchase';
    const TYPE_EXPENSE = 'expense';
    const TYPE_PAYMENT = 'payment';
    const TYPE_RECEIPT = 'receipt';

    protected $fillable = [
        'transaction_number',
        'type',
        'amount',
        'payment_method',
        'reference_type',
        'reference_id',
        'transaction_date',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}