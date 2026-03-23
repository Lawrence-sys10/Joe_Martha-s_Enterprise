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
        // Get today's date
        $today = now()->toDateString();
        $thisMonth = now()->startOfMonth()->toDateString();
        $thisYear = now()->startOfYear()->toDateString();
        
        // Today's Profit & Loss
        $todaySales = \App\Models\Sale::whereDate('sale_date', $today)
            ->where('status', 'completed')
            ->sum('total');
        
        $todayCost = \App\Models\SaleItem::whereHas('sale', function($q) use ($today) {
            $q->whereDate('sale_date', $today)->where('status', 'completed');
        })->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(\DB::raw('SUM(sale_items.quantity * products.cost_price) as total_cost'))
            ->first()->total_cost ?? 0;
        
        $todayProfit = $todaySales - $todayCost;
        $todayMargin = $todaySales > 0 ? ($todayProfit / $todaySales) * 100 : 0;
        
        // Monthly Profit & Loss
        $monthlySales = \App\Models\Sale::whereBetween('sale_date', [$thisMonth, $today])
            ->where('status', 'completed')
            ->sum('total');
        
        $monthlyCost = \App\Models\SaleItem::whereHas('sale', function($q) use ($thisMonth, $today) {
            $q->whereBetween('sale_date', [$thisMonth, $today])->where('status', 'completed');
        })->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(\DB::raw('SUM(sale_items.quantity * products.cost_price) as total_cost'))
            ->first()->total_cost ?? 0;
        
        $monthlyProfit = $monthlySales - $monthlyCost;
        $monthlyMargin = $monthlySales > 0 ? ($monthlyProfit / $monthlySales) * 100 : 0;
        
        // Yearly Profit & Loss
        $yearlySales = \App\Models\Sale::whereBetween('sale_date', [$thisYear, $today])
            ->where('status', 'completed')
            ->sum('total');
        
        $yearlyCost = \App\Models\SaleItem::whereHas('sale', function($q) use ($thisYear, $today) {
            $q->whereBetween('sale_date', [$thisYear, $today])->where('status', 'completed');
        })->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(\DB::raw('SUM(sale_items.quantity * products.cost_price) as total_cost'))
            ->first()->total_cost ?? 0;
        
        $yearlyProfit = $yearlySales - $yearlyCost;
        $yearlyMargin = $yearlySales > 0 ? ($yearlyProfit / $yearlySales) * 100 : 0;
        
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
        
        // Top products
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereMonth('sales.sale_date', now()->month)
            ->whereYear('sales.sale_date', now()->year)
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