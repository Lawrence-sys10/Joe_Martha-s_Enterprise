<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    const METHOD_CASH = 'cash';
    const METHOD_MOBILE_MONEY = 'mobile_money';
    const METHOD_BANK = 'bank';
    const METHOD_CREDIT = 'credit';

    protected $fillable = [
        'sale_id',
        'amount',
        'payment_method',
        'reference_number',
        'payment_date',
        'notes',
        'user_id',
        'transaction_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}