<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierPaymentController extends Controller
{
    /**
     * Display a listing of supplier payments.
     */
    public function index(Request $request)
    {
        $query = PurchasePayment::with(['purchase.supplier', 'user'])
            ->orderBy('payment_date', 'desc');
        
        // Apply filters
        if ($request->filled('supplier_id')) {
            $query->whereHas('purchase', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('payment_date', '<=', $request->end_date);
        }
        
        // Get totals BEFORE pagination for summary cards
        $totalPaymentsAmount = $query->sum('amount');
        $totalTransactionsCount = $query->count();
        $avgPayment = $totalTransactionsCount > 0 ? $totalPaymentsAmount / $totalTransactionsCount : 0;
        $uniqueSuppliers = $query->distinct()->pluck('purchase_id')
            ->filter()
            ->map(function($purchaseId) {
                return Purchase::find($purchaseId)?->supplier_id;
            })
            ->filter()
            ->unique()
            ->count();
        
        // Now paginate for the table
        $payments = $query->paginate(20);
        
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        
        return view('reports.supplier-payments', compact('payments', 'suppliers', 'totalPaymentsAmount', 'totalTransactionsCount', 'avgPayment', 'uniqueSuppliers'));
    }
    
    /**
     * Show the form for creating a new payment.
     */
    public function create($supplierId = null)
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $selectedSupplier = $supplierId ? Supplier::find($supplierId) : null;
        
        return view('reports.supplier-payments-create', compact('suppliers', 'selectedSupplier'));
    }
    
    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_id' => 'nullable|exists:purchases,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Generate payment number
            $paymentNumber = 'SPMT-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            $payment = PurchasePayment::create([
                'purchase_id' => $request->purchase_id,
                'payment_number' => $paymentNumber,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'user_id' => auth()->id(),
            ]);
            
            // Update supplier balance if purchase exists
            if ($request->purchase_id) {
                $purchase = Purchase::find($request->purchase_id);
                if ($purchase && $purchase->supplier) {
                    $supplier = $purchase->supplier;
                    $supplier->current_balance -= $request->amount;
                    $supplier->save();
                }
            } else if ($request->supplier_id) {
                // If payment is not tied to a specific purchase, update supplier balance directly
                $supplier = Supplier::find($request->supplier_id);
                $supplier->current_balance -= $request->amount;
                $supplier->save();
            }
            
            DB::commit();
            
            return redirect()->route('reports.supplier-payments')
                ->with('success', 'Payment recorded successfully! Payment #: ' . $paymentNumber);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Display the specified payment.
     */
    public function show($id)
    {
        $payment = PurchasePayment::with(['purchase.supplier', 'user'])->findOrFail($id);
        return view('payments.show', compact('payment'));
    }
    
    /**
     * Print payment receipt.
     */
    public function printReceipt($id)
    {
        $payment = PurchasePayment::with(['purchase.supplier', 'user'])->findOrFail($id);
        return view('payments.receipt', compact('payment'));
    }
}