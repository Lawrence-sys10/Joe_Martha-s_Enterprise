<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = SupplierPayment::with('supplier', 'user');
        
        if ($request->get('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        if ($request->get('start_date')) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }
        
        if ($request->get('end_date')) {
            $query->whereDate('payment_date', '<=', $request->end_date);
        }
        
        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);
        $suppliers = Supplier::where('is_active', true)->get();
        
        return view('supplier-payments.index', compact('payments', 'suppliers'));
    }

    public function create(Supplier $supplier)
    {
        return view('supplier-payments.create', compact('supplier'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Generate payment number
            $paymentNumber = $this->generatePaymentNumber();
            
            $payment = SupplierPayment::create([
                'supplier_id' => $request->supplier_id,
                'payment_number' => $paymentNumber,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'user_id' => auth()->id(),
            ]);
            
            // Update supplier balance
            $supplier = Supplier::find($request->supplier_id);
            $supplier->current_balance -= $request->amount;
            $supplier->save();
            
            return redirect()->route('supplier-payments.show', $payment)
                ->with('success', 'Payment recorded successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(SupplierPayment $supplierPayment)
    {
        $supplierPayment->load('supplier', 'user');
        return view('supplier-payments.show', compact('supplierPayment'));
    }

    public function printReceipt(SupplierPayment $supplierPayment)
    {
        $supplierPayment->load('supplier', 'user');
        $pdf = Pdf::loadView('supplier-payments.receipt', compact('supplierPayment'));
        $pdf->setPaper('A6', 'portrait');
        return $pdf->download("payment-receipt-{$supplierPayment->payment_number}.pdf");
    }

    private function generatePaymentNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastPayment = SupplierPayment::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastPayment ? intval(substr($lastPayment->payment_number, -4)) + 1 : 1;
        
        return "PMT-{$year}{$month}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}