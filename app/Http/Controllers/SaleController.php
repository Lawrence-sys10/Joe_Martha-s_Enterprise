<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\SalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    protected $salesService;

    public function __construct(SalesService $salesService)
    {
        $this->salesService = $salesService;
    }

    public function index(Request $request)
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles);
        
        $query = Sale::with(['customer', 'items', 'payments'])
            ->where('status', 'completed');
        
        if ($isAttendant) {
            // Attendants only see today's sales
            $query->whereDate('created_at', today());
        } else {
            // Apply filters only for non-attendants
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }
            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }
        }
        
        $sales = $query->latest()->paginate(20);
        
        // Calculate statistics
        if ($isAttendant) {
            // Attendants only see today's stats
            $totalSales = Sale::whereDate('created_at', today())->where('status', 'completed')->sum('total');
            $todaySales = Sale::whereDate('created_at', today())->where('status', 'completed')->sum('total');
            $todayCount = Sale::whereDate('created_at', today())->where('status', 'completed')->count();
            $totalTransactions = Sale::whereDate('created_at', today())->where('status', 'completed')->count();
            $avgSaleValue = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
            
            // Attendants don't see credit stats
            $pendingCredit = 0;
            $partialCredit = 0;
            $paidCredit = 0;
        } else {
            // Full stats for admins/managers
            $totalSales = Sale::where('status', 'completed')->sum('total');
            $todaySales = Sale::whereDate('created_at', today())->where('status', 'completed')->sum('total');
            $todayCount = Sale::whereDate('created_at', today())->where('status', 'completed')->count();
            $totalTransactions = Sale::count();
            $avgSaleValue = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
            
            // Calculate credit sales statistics
            $pendingCredit = Sale::where('payment_method', 'credit')
                ->where('payment_status', 'pending')
                ->sum('total');
            
            $partialCredit = \App\Models\Payment::whereHas('sale', function($query) {
                $query->where('payment_method', 'credit')
                    ->where('payment_status', 'partial');
            })->sum('amount');
            
            $paidCredit = Sale::where('payment_method', 'credit')
                ->where('payment_status', 'paid')
                ->sum('total');
        }
        
        return view('sales.index', compact('sales', 'totalSales', 'todaySales', 'todayCount', 'totalTransactions', 'avgSaleValue', 'pendingCredit', 'partialCredit', 'paidCredit', 'isAttendant'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->get();
        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|in:cash,mobile_money,credit,bank',
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            
            $sale = $this->salesService->createSale($request->all(), $request->items);
            
            // Set paid_amount from request
            $sale->paid_amount = $request->paid_amount;
            
            // Unified payment logic - no separation for credit sales
            if ($request->paid_amount >= $sale->total) {
                $sale->payment_status = 'paid';
                $sale->status = 'completed';
            } elseif ($request->paid_amount > 0) {
                $sale->payment_status = 'partial';
                $sale->status = 'completed';
            } else {
                $sale->payment_status = 'pending';
                $sale->status = 'completed';
            }
            
            $sale->save();
            
            DB::commit();
            
            // For AJAX/POS requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sale completed successfully!',
                    'sale_id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'payment_status' => $sale->payment_status,
                    'status' => $sale->status,
                    'total' => $sale->total,
                    'paid_amount' => $sale->paid_amount,
                    'balance_due' => $sale->total - $sale->paid_amount,
                    'redirect' => route('sales.index')
                ]);
            }
            
            // For regular form submission
            return redirect()->route('sales.index')
                ->with('success', 'Sale completed successfully! Invoice: ' . $sale->invoice_number);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to complete sale: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Sale $sale)
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles);
        
        // Attendants can only view today's sales
        if ($isAttendant && $sale->created_at->toDateString() != today()->toDateString()) {
            abort(403, 'You can only view today\'s sales.');
        }
        
        $sale->load('customer', 'user', 'items.product', 'payments');
        
        // Calculate total paid from payments table
        $totalPaidFromPayments = $sale->payments()->sum('amount');
        
        // Update the sale's paid_amount to match the actual payments
        $sale->paid_amount = $totalPaidFromPayments;
        
        // Calculate remaining balance
        $remainingBalance = $sale->total - $totalPaidFromPayments;
        
        // Update payment status based on actual payments
        if ($totalPaidFromPayments >= $sale->total) {
            $sale->payment_status = 'paid';
            $sale->status = 'completed';
        } elseif ($totalPaidFromPayments > 0) {
            $sale->payment_status = 'partial';
            $sale->status = 'completed';
        } else {
            $sale->payment_status = 'pending';
            $sale->status = 'completed';
        }
        $sale->save();
        
        return view('sales.show', compact('sale', 'remainingBalance', 'totalPaidFromPayments', 'isAttendant'));
    }

    public function printReceipt(Sale $sale)
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles);
        
        // Attendants can only print receipts for today's sales
        if ($isAttendant && $sale->created_at->toDateString() != today()->toDateString()) {
            abort(403, 'You can only print receipts for today\'s sales.');
        }
        
        // Only allow printing if payment is fully paid
        $totalPaid = $sale->payments()->sum('amount');
        if ($totalPaid < $sale->total) {
            return redirect()->back()->with('error', 'Receipt can only be printed for fully paid sales.');
        }
        
        return $this->salesService->generateReceipt($sale);
    }
    
    public function void(Request $request, Sale $sale)
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles);
        
        // Attendants cannot void sales
        if ($isAttendant) {
            abort(403, 'You do not have permission to void sales.');
        }
        
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            DB::beginTransaction();
            
            // Restore stock for voided sale
            foreach ($sale->items as $item) {
                $product = $item->product;
                $beforeQuantity = $product->stock_quantity;
                $afterQuantity = $beforeQuantity + $item->quantity;
                
                $product->stock_quantity = $afterQuantity;
                $product->save();
                
                // Record stock movement
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => StockMovement::TYPE_RETURN,
                    'quantity' => $item->quantity,
                    'before_quantity' => $beforeQuantity,
                    'after_quantity' => $afterQuantity,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'notes' => "Void sale #{$sale->invoice_number}: {$request->reason}",
                    'user_id' => auth()->id(),
                ]);
            }
            
            $sale->status = Sale::STATUS_CANCELLED;
            $sale->payment_status = 'cancelled';
            $sale->notes = ($sale->notes ? $sale->notes . "\n" : '') . "Voided: {$request->reason}";
            $sale->save();
            
            DB::commit();
            
            return redirect()->route('sales.index')
                ->with('success', 'Sale voided successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to void sale: ' . $e->getMessage());
        }
    }
    
    public function updatePaymentStatus(Request $request, Sale $sale)
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles);
        
        // Attendants cannot update payment status
        if ($isAttendant) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update payment status.'
            ], 403);
        }
        
        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
        ]);
        
        try {
            DB::beginTransaction();
            
            $totalPaid = $sale->payments()->sum('amount') + $request->paid_amount;
            
            // Unified payment status logic
            if ($totalPaid >= $sale->total) {
                $sale->payment_status = 'paid';
                $sale->status = 'completed';
            } elseif ($totalPaid > 0) {
                $sale->payment_status = 'partial';
                $sale->status = 'completed';
            } else {
                $sale->payment_status = 'pending';
                $sale->status = 'completed';
            }
            $sale->paid_amount = $totalPaid;
            $sale->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'payment_status' => $sale->payment_status,
                'status' => $sale->status,
                'total_paid' => $totalPaid,
                'balance_due' => $sale->total - $totalPaid,
                'message' => 'Payment status updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}