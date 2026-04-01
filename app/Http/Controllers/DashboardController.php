<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\SalesService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $productService;
    protected $salesService;
    protected $inventoryService;

    public function __construct(
        ProductService $productService,
        SalesService $salesService,
        InventoryService $inventoryService
    ) {
        $this->productService = $productService;
        $this->salesService = $salesService;
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        // Get date ranges with proper time boundaries
        $todayStart = now()->startOfDay()->format('Y-m-d H:i:s');
        $todayEnd = now()->endOfDay()->format('Y-m-d H:i:s');
        
        $monthStart = now()->startOfMonth()->format('Y-m-d H:i:s');
        $monthEnd = now()->endOfMonth()->format('Y-m-d H:i:s');
        
        $yearStart = now()->startOfYear()->format('Y-m-d H:i:s');
        $yearEnd = now()->endOfYear()->format('Y-m-d H:i:s');
        
        // ========== PROFIT & LOSS CALCULATIONS ==========
        
        // Today's Profit & Loss
        $todaySales = \App\Models\Sale::whereBetween('sale_date', [$todayStart, $todayEnd])
            ->where('status', 'completed')
            ->sum('total');
        
        $todayCost = \App\Models\SaleItem::whereHas('sale', function($q) use ($todayStart, $todayEnd) {
                $q->whereBetween('sale_date', [$todayStart, $todayEnd])
                  ->where('status', 'completed');
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(\DB::raw('SUM(sale_items.quantity * products.cost_price) as total_cost'))
            ->first()->total_cost ?? 0;
        
        $todayProfit = $todaySales - $todayCost;
        $todayMargin = $todaySales > 0 ? ($todayProfit / $todaySales) * 100 : 0;
        
        // Monthly Profit & Loss (Full Month)
        $monthlySales = \App\Models\Sale::whereBetween('sale_date', [$monthStart, $monthEnd])
            ->where('status', 'completed')
            ->sum('total');
        
        $monthlyCost = \App\Models\SaleItem::whereHas('sale', function($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('sale_date', [$monthStart, $monthEnd])
                  ->where('status', 'completed');
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(\DB::raw('SUM(sale_items.quantity * products.cost_price) as total_cost'))
            ->first()->total_cost ?? 0;
        
        $monthlyProfit = $monthlySales - $monthlyCost;
        $monthlyMargin = $monthlySales > 0 ? ($monthlyProfit / $monthlySales) * 100 : 0;
        
        // Yearly Profit & Loss (Full Year)
        $yearlySales = \App\Models\Sale::whereBetween('sale_date', [$yearStart, $yearEnd])
            ->where('status', 'completed')
            ->sum('total');
        
        $yearlyCost = \App\Models\SaleItem::whereHas('sale', function($q) use ($yearStart, $yearEnd) {
                $q->whereBetween('sale_date', [$yearStart, $yearEnd])
                  ->where('status', 'completed');
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(\DB::raw('SUM(sale_items.quantity * products.cost_price) as total_cost'))
            ->first()->total_cost ?? 0;
        
        $yearlyProfit = $yearlySales - $yearlyCost;
        $yearlyMargin = $yearlySales > 0 ? ($yearlyProfit / $yearlySales) * 100 : 0;
        
        // ========== CASH RECEIVED & OUTSTANDING CREDIT CALCULATIONS ==========
        
        // Today's breakdown
        // FULLY PAID SALES (cash sales + fully paid credit sales)
        $todayFullyPaid = \App\Models\Sale::whereBetween('sale_date', [$todayStart, $todayEnd])
            ->where('payment_status', 'paid')
            ->sum('total');
        
        // ACTUAL AMOUNT COLLECTED from partially paid sales
        $todayPartialCollected = \App\Models\Payment::whereDate('payment_date', now()->toDateString())
            ->whereHas('sale', function($q) {
                $q->where('payment_status', 'partial');
            })->sum('amount');
        
        // TOTAL CASH RECEIVED = Fully Paid + Payments received on Partial Sales
        $todayCashReceived = $todayFullyPaid + $todayPartialCollected;
        
        // OUTSTANDING CREDIT = (Credit sales with NO payment received) + (Remaining balance on partially paid credit sales)
        
        // Part 1: Credit sales with NO payment received (pending status)
        $todayPendingCredit = \App\Models\Sale::whereBetween('sale_date', [$todayStart, $todayEnd])
            ->where('payment_method', 'credit')
            ->where('payment_status', 'pending')
            ->sum('total');
        
        // Part 2: Remaining balance on partially paid credit sales
        $todayPartialRemaining = 0;
        $todayPartialCreditSales = \App\Models\Sale::whereBetween('sale_date', [$todayStart, $todayEnd])
            ->where('payment_method', 'credit')
            ->where('payment_status', 'partial')
            ->get();
        
        foreach ($todayPartialCreditSales as $sale) {
            $paidAmount = $sale->payments()->sum('amount');
            $remaining = $sale->total - $paidAmount;
            if ($remaining > 0) {
                $todayPartialRemaining += $remaining;
            }
        }
        
        // Total Outstanding Credit = Pending Credit + Remaining Balance on Partial Credit
        $todayOutstandingCredit = $todayPendingCredit + $todayPartialRemaining;
        
        // Monthly breakdown
        $monthlyFullyPaid = \App\Models\Sale::whereBetween('sale_date', [$monthStart, $monthEnd])
            ->where('payment_status', 'paid')
            ->sum('total');
        
        $monthlyPartialCollected = \App\Models\Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->whereHas('sale', function($q) {
                $q->where('payment_status', 'partial');
            })->sum('amount');
        
        $monthlyCashReceived = $monthlyFullyPaid + $monthlyPartialCollected;
        
        $monthlyPendingCredit = \App\Models\Sale::whereBetween('sale_date', [$monthStart, $monthEnd])
            ->where('payment_method', 'credit')
            ->where('payment_status', 'pending')
            ->sum('total');
        
        $monthlyPartialRemaining = 0;
        $monthlyPartialCreditSales = \App\Models\Sale::whereBetween('sale_date', [$monthStart, $monthEnd])
            ->where('payment_method', 'credit')
            ->where('payment_status', 'partial')
            ->get();
        
        foreach ($monthlyPartialCreditSales as $sale) {
            $paidAmount = $sale->payments()->sum('amount');
            $remaining = $sale->total - $paidAmount;
            if ($remaining > 0) {
                $monthlyPartialRemaining += $remaining;
            }
        }
        
        $monthlyOutstandingCredit = $monthlyPendingCredit + $monthlyPartialRemaining;
        
        // Yearly breakdown
        $yearlyFullyPaid = \App\Models\Sale::whereBetween('sale_date', [$yearStart, $yearEnd])
            ->where('payment_status', 'paid')
            ->sum('total');
        
        $yearlyPartialCollected = \App\Models\Payment::whereYear('payment_date', now()->year)
            ->whereHas('sale', function($q) {
                $q->where('payment_status', 'partial');
            })->sum('amount');
        
        $yearlyCashReceived = $yearlyFullyPaid + $yearlyPartialCollected;
        
        $yearlyPendingCredit = \App\Models\Sale::whereBetween('sale_date', [$yearStart, $yearEnd])
            ->where('payment_method', 'credit')
            ->where('payment_status', 'pending')
            ->sum('total');
        
        $yearlyPartialRemaining = 0;
        $yearlyPartialCreditSales = \App\Models\Sale::whereBetween('sale_date', [$yearStart, $yearEnd])
            ->where('payment_method', 'credit')
            ->where('payment_status', 'partial')
            ->get();
        
        foreach ($yearlyPartialCreditSales as $sale) {
            $paidAmount = $sale->payments()->sum('amount');
            $remaining = $sale->total - $paidAmount;
            if ($remaining > 0) {
                $yearlyPartialRemaining += $remaining;
            }
        }
        
        $yearlyOutstandingCredit = $yearlyPendingCredit + $yearlyPartialRemaining;
        
        // ========== STOCK & OTHER DATA ==========
        
        // Get low stock products
        $lowStockProducts = \App\Models\Product::whereRaw('stock_quantity <= minimum_stock')
            ->where('stock_quantity', '>', 0)
            ->where('is_active', true)
            ->get();
        
        // Get out of stock products
        $outOfStockProducts = \App\Models\Product::where('stock_quantity', '<=', 0)
            ->where('is_active', true)
            ->get();
        
        // Supplier balance
        $supplierBalance = \App\Models\Supplier::sum('current_balance');
        
        // Stock value
        $stockValue = \App\Models\Product::sum(\DB::raw('stock_quantity * cost_price'));
        
        // Recent sales
        $recentSales = \App\Models\Sale::with('customer')->latest()->limit(5)->get();
        
        // Recent purchases
        $recentPurchases = \App\Models\Purchase::with('supplier')->latest()->limit(5)->get();
        
        // Monthly purchases
        $monthlyPurchases = \App\Models\Purchase::whereBetween('purchase_date', [now()->startOfMonth(), now()->endOfMonth()])->sum('total');
        $pendingPayments = \App\Models\Purchase::where('payment_status', '!=', 'paid')->sum('total');
        $totalPurchases = \App\Models\Purchase::sum('total');
        
        // Top products (current month)
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$monthStart, $monthEnd])
            ->where('sales.status', 'completed')
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard.index', compact(
            // Profit & Loss
            'todaySales',
            'todayCost',
            'todayProfit',
            'todayMargin',
            'monthlySales',
            'monthlyCost',
            'monthlyProfit',
            'monthlyMargin',
            'yearlySales',
            'yearlyCost',
            'yearlyProfit',
            'yearlyMargin',
            // Cash Received & Outstanding Credit
            'todayCashReceived',
            'todayOutstandingCredit',
            'monthlyCashReceived',
            'monthlyOutstandingCredit',
            'yearlyCashReceived',
            'yearlyOutstandingCredit',
            // Stock & Other
            'lowStockProducts',
            'outOfStockProducts',
            'supplierBalance',
            'stockValue',
            'recentSales',
            'recentPurchases',
            'monthlyPurchases',
            'pendingPayments',
            'totalPurchases',
            'topProducts'
        ));
    }
}