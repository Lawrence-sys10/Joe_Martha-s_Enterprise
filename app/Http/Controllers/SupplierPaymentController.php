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
    // Start with base query - get ALL payments with their relationships
    // Sort by payment_date first, then by created_at for same-day payments
    $query = PurchasePayment::with(['purchase.supplier', 'user'])
        ->orderBy('payment_date', 'desc')
        ->orderBy('created_at', 'desc');  // This ensures newest recordings show first
    
    // Debug logging
    \Log::info('SupplierPaymentController@index - Initial query count: ' . $query->count());
    
    // Apply filters
    if ($request->filled('supplier_id') && $request->supplier_id != '') {
        $query->whereHas('purchase', function($q) use ($request) {
            $q->where('supplier_id', $request->supplier_id);
        });
        \Log::info('Filtering by supplier_id: ' . $request->supplier_id);
    }
    
    if ($request->filled('payment_method') && $request->payment_method != '') {
        $query->where('payment_method', $request->payment_method);
        \Log::info('Filtering by payment_method: ' . $request->payment_method);
    }
    
    if ($request->filled('start_date') && $request->start_date != '') {
        $query->whereDate('payment_date', '>=', $request->start_date);
        \Log::info('Filtering by start_date: ' . $request->start_date);
    }
    
    if ($request->filled('end_date') && $request->end_date != '') {
        $query->whereDate('payment_date', '<=', $request->end_date);
        \Log::info('Filtering by end_date: ' . $request->end_date);
    }
    
    // Debug logging after filters
    $countAfterFilters = $query->count();
    \Log::info('After filters query count: ' . $countAfterFilters);
    
    // Get the filtered payments for display
    $payments = $query->paginate(20);
    
    // Debug: Log actual payment data
    if ($payments->count() > 0) {
        \Log::info('Payments found: ' . $payments->count());
        foreach ($payments as $payment) {
            \Log::info('Payment ID: ' . $payment->id . 
                      ', Number: ' . $payment->payment_number . 
                      ', Amount: ' . $payment->amount .
                      ', Purchase ID: ' . $payment->purchase_id .
                      ', Created: ' . $payment->created_at);
        }
    } else {
        \Log::warning('No payments found in the query result!');
    }
    
    // Calculate totals from the filtered query (not paginated)
    $totalPaymentsAmount = $query->sum('amount');
    $totalTransactionsCount = $query->count();
    $avgPayment = $totalTransactionsCount > 0 ? $totalPaymentsAmount / $totalTransactionsCount : 0;
    
    // Calculate unique suppliers from the filtered payments
    $uniqueSuppliers = $query->get()->pluck('purchase.supplier_id')
        ->filter()
        ->unique()
        ->count();
    
    $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
    
    // Pass data to view
    return view('reports.supplier-payments', compact(
        'payments', 
        'suppliers', 
        'totalPaymentsAmount', 
        'totalTransactionsCount', 
        'avgPayment', 
        'uniqueSuppliers'
    ));
}
    
    /**
     * Show the form for creating a new payment.
     */
    public function create($supplierId = null, $purchaseId = null)
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $selectedSupplier = $supplierId ? Supplier::find($supplierId) : null;
        $selectedPurchase = null;
        
        if ($purchaseId) {
            $selectedPurchase = Purchase::with('supplier')->find($purchaseId);
            $selectedSupplier = $selectedPurchase ? $selectedPurchase->supplier : null;
        }
        
        // Get unpaid purchases for the selected supplier
        $unpaidPurchases = collect();
        if ($selectedSupplier) {
            $unpaidPurchases = Purchase::where('supplier_id', $selectedSupplier->id)
                ->where('payment_status', '!=', 'paid')
                ->whereRaw('total > COALESCE((SELECT SUM(amount) FROM purchase_payments WHERE purchase_id = purchases.id), 0)')
                ->orderBy('purchase_date', 'desc')
                ->get()
                ->map(function($purchase) {
                    $paidAmount = $purchase->payments()->sum('amount');
                    $purchase->remaining_balance = $purchase->total - $paidAmount;
                    return $purchase;
                });
        }
        
        return view('reports.supplier-payments-create', compact('suppliers', 'selectedSupplier', 'selectedPurchase', 'unpaidPurchases'));
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
            // Generate payment number (match existing format PPMT-)
            $paymentNumber = 'PPMT-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
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
            
            // Update supplier balance
            $supplier = Supplier::find($request->supplier_id);
            $supplier->current_balance -= $request->amount;
            $supplier->save();
            
            // If linked to a specific purchase, update purchase payment status
            if ($request->purchase_id) {
                $purchase = Purchase::find($request->purchase_id);
                $totalPaid = $purchase->payments()->sum('amount');
                
                if ($totalPaid >= $purchase->total) {
                    $purchase->payment_status = 'paid';
                } else {
                    $purchase->payment_status = 'partial';
                }
                $purchase->save();
            }
            
            DB::commit();
            
            // Log success
            \Log::info('Payment created successfully: ' . $paymentNumber);
            
            // Redirect to the purchase show page if purchase_id exists
            if ($request->purchase_id) {
                return redirect()->route('purchases.show', $request->purchase_id)
                    ->with('success', 'Payment recorded successfully! Payment #: ' . $paymentNumber);
            }
            
            return redirect()->route('reports.supplier-payments')
                ->with('success', 'Payment recorded successfully! Payment #: ' . $paymentNumber);
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Display the specified payment - Redirect to purchase show page
     */
    public function show($id)
    {
        $payment = PurchasePayment::with('purchase')->findOrFail($id);
        
        // If payment is linked to a purchase, redirect to the purchase show page
        if ($payment->purchase) {
            return redirect()->route('purchases.show', $payment->purchase_id)
                ->with('info', 'Viewing purchase order #' . $payment->purchase->invoice_number);
        }
        
        // If no purchase linked, redirect to payments report
        return redirect()->route('reports.supplier-payments')
            ->with('info', 'Payment details can be viewed in the purchase record.');
    }
    
    /**
     * API endpoint to get unpaid purchases for a supplier
     */
    public function getUnpaidPurchases($supplierId)
    {
        $purchases = Purchase::where('supplier_id', $supplierId)
            ->where('payment_status', '!=', 'paid')
            ->whereRaw('total > COALESCE((SELECT SUM(amount) FROM purchase_payments WHERE purchase_id = purchases.id), 0)')
            ->orderBy('purchase_date', 'desc')
            ->get()
            ->map(function($purchase) {
                $paidAmount = $purchase->payments()->sum('amount');
                $purchase->remaining_balance = $purchase->total - $paidAmount;
                return [
                    'id' => $purchase->id,
                    'invoice_number' => $purchase->invoice_number,
                    'purchase_date' => $purchase->purchase_date->format('Y-m-d'),
                    'total' => $purchase->total,
                    'remaining_balance' => $purchase->remaining_balance
                ];
            });
        
        return response()->json($purchases);
    }
}