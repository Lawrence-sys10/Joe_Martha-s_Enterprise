<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'transaction_type',
        'amount',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'created_at' => 'datetime'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record($supplier, $type, $amount, $referenceType, $referenceId, $notes = null)
    {
        $beforeBalance = $supplier->current_balance - ($type === 'payment' ? $amount : 0);
        
        return self::create([
            'supplier_id' => $supplier->id,
            'transaction_type' => $type,
            'amount' => $amount,
            'balance_before' => max(0, $beforeBalance),
            'balance_after' => $supplier->current_balance,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'user_id' => auth()->id(),
        ]);
    }
}