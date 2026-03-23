<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'transaction_type',
        'quantity',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'balance_before' => 'integer',
        'balance_after' => 'integer',
        'created_at' => 'datetime'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record($product, $type, $quantity, $referenceType, $referenceId, $notes = null)
    {
        $beforeBalance = $product->stock_quantity - ($type === 'sale' ? $quantity : 0);
        $beforeBalance = $type === 'purchase' ? $product->stock_quantity - $quantity : $beforeBalance;
        
        return self::create([
            'product_id' => $product->id,
            'transaction_type' => $type,
            'quantity' => $quantity,
            'balance_before' => max(0, $beforeBalance),
            'balance_after' => $product->stock_quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'user_id' => auth()->id(),
        ]);
    }
}