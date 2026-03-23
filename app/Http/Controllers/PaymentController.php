<?php

namespace App\Http\Controllers;

use App\Models\PurchasePayment;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchasePayment::with(['purchase.supplier', 'user']);
        
        if ($request->get('supplier_id')) {
            $query->whereHas('purchase', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }
        
        if ($request->get('start_date')) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }
        
        if ($request->get('end_date')) {
            $query->whereDate('payment_date', '<=', $request->end_date);
        }
        
        if ($request->get('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);
        
        $totalPayments = PurchasePayment::count();
        $totalAmount = PurchasePayment::sum('amount');
        $suppliersPaid = PurchasePayment::distinct('purchase_id')->count('purchase_id');
        $suppliers = Supplier::where('is_active', true)->get();
        
        $paymentMethods = [
            'cash' => PurchasePayment::where('payment_method', 'cash')->sum('amount'),
            'bank_transfer' => PurchasePayment::where('payment_method', 'bank_transfer')->sum('amount'),
            'mobile_money' => PurchasePayment::where('payment_method', 'mobile_money')->sum('amount'),
            'cheque' => PurchasePayment::where('payment_method', 'cheque')->sum('amount'),
        ];
        
        return view('payments.index', compact('payments', 'totalPayments', 'totalAmount', 'suppliersPaid', 'suppliers', 'paymentMethods'));
    }
    
    public function show(PurchasePayment $payment)
    {
        $payment->load('purchase.supplier', 'user');
        return view('payments.show', compact('payment'));
    }
    
    public function printReceipt(PurchasePayment $payment)
    {
        $payment->load('purchase.supplier', 'user');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payments.receipt', compact('payment'));
        $pdf->setPaper('A6', 'portrait');
        return $pdf->download("payment-receipt-{$payment->payment_number}.pdf");
    }
}