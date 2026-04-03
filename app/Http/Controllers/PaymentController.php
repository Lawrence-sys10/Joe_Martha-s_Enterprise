<?php

namespace App\Http\Controllers;

use App\Models\PurchasePayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Redirect to supplier payments report
     */
    public function index()
    {
        return redirect()->route('reports.supplier-payments')
            ->with('info', 'View all payments in the Supplier Payments Report.');
    }
    
    /**
     * Show payment details
     */
   public function show($id)
{
    $payment = PurchasePayment::with(['purchase.supplier'])->findOrFail($id);
    $purchase = $payment->purchase;
    
    // Calculate balance if needed
    $totalPaid = $purchase->payments()->sum('amount');
    $balance = $purchase->total - $totalPaid;
    
    return view('purchases.show', compact('purchase', 'balance'));
}
    
    /**
     * Print payment receipt
     */
    public function printReceipt($id)
    {
        $payment = PurchasePayment::with('purchase.supplier')->findOrFail($id);
        return view('payments.receipt', compact('payment'));
    }
}
