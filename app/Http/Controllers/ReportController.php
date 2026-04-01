<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Services\SalesService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use App\Exports\CustomerDebtExport;
use App\Exports\SupplierBalanceExport;
use App\Services\CashBalanceService;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected $reportService;
    protected $salesService;
    protected $inventoryService;

    public function __construct(
        ReportService $reportService,
        SalesService $salesService,
        InventoryService $inventoryService
    ) {
        $this->reportService = $reportService;
        $this->salesService = $salesService;
        $this->inventoryService = $inventoryService;
    }

    public function dailySales(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        
        // Convert to datetime with proper time boundaries
        $startDateTime = \Carbon\Carbon::parse($date)->startOfDay();
        $endDateTime = \Carbon\Carbon::parse($date)->endOfDay();
        
        // Get sales for the selected date - INCLUDE all sales regardless of payment status
        $sales = Sale::with(['customer', 'items.product'])
            ->whereBetween('sale_date', [$startDateTime, $endDateTime])
            ->orderBy('sale_date', 'desc')
            ->get();
        
        // Calculate summary
        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total');
        $totalTax = $sales->sum('tax');
        $totalDiscount = $sales->sum('discount');
        $averageSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;
        
        $summary = collect([(object)[
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'total_tax' => $totalTax,
            'total_discount' => $totalDiscount,
            'average_sale' => $averageSale
        ]]);
        
        return view('reports.daily-sales', compact('sales', 'date', 'summary'));
    }

    public function monthlySales(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        
        // Get the first and last day of the selected month
        $startDate = \Carbon\Carbon::parse($month)->startOfMonth();
        $endDate = \Carbon\Carbon::parse($month)->endOfMonth();
        
        // Get sales for the selected month with eager loading
        $sales = Sale::with(['customer', 'items.product', 'payments'])
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->orderBy('sale_date', 'desc')
            ->paginate(20);
        
        // Calculate totals using a separate query for accurate summary (not affected by pagination)
        $totals = Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(*) as total_sales'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('AVG(total) as average_sale')
            )
            ->first();
        
        $totalSales = $totals->total_sales ?? 0;
        $totalRevenue = $totals->total_revenue ?? 0;
        $averageSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;
        
        $summary = collect([(object)[
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'average_sale' => $averageSale
        ]]);
        
        return view('reports.monthly-sales', compact('sales', 'month', 'summary', 'startDate', 'endDate'));
    }

    public function profitLoss(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        // Convert to datetime with proper time boundaries
        $startDateTime = \Carbon\Carbon::parse($startDate)->startOfDay();
        $endDateTime = \Carbon\Carbon::parse($endDate)->endOfDay();
        
        // Calculate Sales Revenue - ALL sales included
        $totalSales = Sale::whereBetween('sale_date', [$startDateTime, $endDateTime])
            ->sum('total');
        
        // Calculate Cost of Goods Sold (from sale items)
        $totalCost = SaleItem::whereHas('sale', function($q) use ($startDateTime, $endDateTime) {
                $q->whereBetween('sale_date', [$startDateTime, $endDateTime]);
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(DB::raw('COALESCE(SUM(sale_items.quantity * products.cost_price), 0) as total_cost'))
            ->first()->total_cost ?? 0;
        
        // Calculate Expenses
        $totalExpenses = Expense::whereBetween('expense_date', [$startDateTime, $endDateTime])
            ->sum('amount');
        
        // Calculate Purchases (for reference)
        $totalPurchases = Purchase::whereBetween('purchase_date', [$startDateTime, $endDateTime])
            ->sum('total');
        
        // Calculate Gross Profit
        $grossProfit = $totalSales - $totalCost;
        
        // Calculate Net Profit
        $netProfit = $grossProfit - $totalExpenses;
        
        // Calculate Profit Margin
        $profitMargin = $totalSales > 0 ? ($grossProfit / $totalSales) * 100 : 0;
        
        // Get daily breakdown for chart
        $dailyData = Sale::whereBetween('sale_date', [$startDateTime, $endDateTime])
            ->select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('SUM(total) as daily_sales'),
                DB::raw('SUM(tax) as daily_tax'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Get top products for the period
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDateTime, $endDateTime])
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();
        
        $profitLoss = [
            'total_sales' => $totalSales,
            'total_cost' => $totalCost,
            'total_purchases' => $totalPurchases,
            'total_expenses' => $totalExpenses,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'profit_margin' => round($profitMargin, 2),
            'daily_data' => $dailyData,
            'top_products' => $topProducts,
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ];
        
        return view('reports.profit-loss', compact('profitLoss', 'startDate', 'endDate'));
    }

    public function stockValuation()
    {
        // Get all active products with complete data including cost_price
        $products = Product::where('is_active', true)
            ->select(
                'id',
                'name',
                'sku',
                'stock_quantity',
                'minimum_stock',
                'maximum_stock',
                'unit',
                'cost_price',
                'unit_price',
                'is_active'
            )
            ->orderBy('name', 'asc')
            ->get();
        
        // Calculate total stock value
        $totalValue = Product::where('is_active', true)
            ->sum(DB::raw('COALESCE(stock_quantity, 0) * COALESCE(cost_price, 0)'));
        
        return view('reports.stock-valuation', compact('products', 'totalValue'));
    }

    public function topProducts(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        $limit = (int) $request->get('limit', 10);
        
        // Convert to datetime with proper time boundaries
        $startDateTime = \Carbon\Carbon::parse($startDate)->startOfDay();
        $endDateTime = \Carbon\Carbon::parse($endDate)->endOfDay();
        
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDateTime, $endDateTime])
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_revenue'),
                DB::raw('COUNT(DISTINCT sale_items.sale_id) as times_sold'),
                DB::raw('AVG(sale_items.unit_price) as avg_price')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
        
        return view('reports.top-products', compact('topProducts', 'startDate', 'endDate', 'limit'));
    }

    public function export(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        return Excel::download(new SalesExport($startDate, $endDate), 'sales-report.xlsx');
    }
    
    public function customerDebt()
    {
        $customers = Customer::where('current_balance', '>', 0)
            ->orderBy('current_balance', 'desc')
            ->paginate(20);
        $totalDebt = Customer::sum('current_balance');
        
        return view('reports.customer-debt', compact('customers', 'totalDebt'));
    }
    
    public function supplierBalance()
    {
        $suppliers = Supplier::where('current_balance', '!=', 0)
            ->orderBy('current_balance', 'desc')
            ->paginate(20);
        $totalBalance = Supplier::sum('current_balance');
        
        return view('reports.supplier-balance', compact('suppliers', 'totalBalance'));
    }
    
    public function cashFlow(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        $cashService = new CashBalanceService();
        $cashFlow = $cashService->getCashFlowStatement($startDate, $endDate);
        $currentBalance = $cashService->getCurrentBalance();
        
        return view('reports.cash-flow', compact('cashFlow', 'currentBalance', 'startDate', 'endDate'));
    }
    
    public function expenseReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        $expenses = Expense::with('category')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->paginate(20);
        
        $totalExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
        
        return view('reports.expense', compact('expenses', 'totalExpenses', 'startDate', 'endDate'));
    }
    
    public function exportCustomerDebt()
    {
        return Excel::download(new CustomerDebtExport(), 'customer-debt.xlsx');
    }
    
    public function exportSupplierBalance()
    {
        return Excel::download(new SupplierBalanceExport(), 'supplier-balance.xlsx');
    }
}