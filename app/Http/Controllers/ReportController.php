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
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles) || in_array('attendant', $userRoles);
        
        // For attendants, force today's date, ignore any date filters
        if ($isAttendant) {
            $date = now()->toDateString();
        } else {
            $date = $request->get('date', now()->toDateString());
        }
        
        $startDateTime = Carbon::parse($date)->startOfDay();
        $endDateTime = Carbon::parse($date)->endOfDay();
        
        $sales = Sale::with(['customer', 'items.product'])
            ->whereBetween('sale_date', [$startDateTime, $endDateTime])
            ->orderBy('sale_date', 'desc')
            ->get();
        
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
        
        return view('reports.daily-sales', compact('sales', 'date', 'summary', 'isAttendant'));
    }

    public function monthlySales(Request $request)
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles) || in_array('attendant', $userRoles);
        
        // Attendants cannot access monthly sales
        if ($isAttendant) {
            abort(403, 'You do not have permission to access monthly sales reports.');
        }
        
        $month = $request->get('month', now()->format('Y-m'));
        
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();
        
        $sales = Sale::with(['customer', 'items.product', 'payments'])
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->orderBy('sale_date', 'desc')
            ->paginate(20);
        
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
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles) || in_array('attendant', $userRoles);
        
        // Attendants cannot access profit & loss
        if ($isAttendant) {
            abort(403, 'You do not have permission to access profit & loss reports.');
        }
        
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();
        
        $totalSales = Sale::whereBetween('sale_date', [$startDateTime, $endDateTime])
            ->sum('total');
        
        $totalCost = SaleItem::whereHas('sale', function($q) use ($startDateTime, $endDateTime) {
                $q->whereBetween('sale_date', [$startDateTime, $endDateTime]);
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(DB::raw('COALESCE(SUM(sale_items.quantity * products.cost_price), 0) as total_cost'))
            ->first()->total_cost ?? 0;
        
        $totalExpenses = Expense::whereBetween('expense_date', [$startDateTime, $endDateTime])
            ->sum('amount');
        
        $totalPurchases = Purchase::whereBetween('purchase_date', [$startDateTime, $endDateTime])
            ->sum('total');
        
        $grossProfit = $totalSales - $totalCost;
        $netProfit = $grossProfit - $totalExpenses;
        $profitMargin = $totalSales > 0 ? ($grossProfit / $totalSales) * 100 : 0;
        
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

    public function stockValuation(Request $request)
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles) || in_array('attendant', $userRoles);
        
        // Attendants cannot access stock valuation
        if ($isAttendant) {
            abort(403, 'You do not have permission to access stock valuation reports.');
        }
        
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
        
        $totalValue = Product::where('is_active', true)
            ->sum(DB::raw('COALESCE(stock_quantity, 0) * COALESCE(cost_price, 0)'));
        
        return view('reports.stock-valuation', compact('products', 'totalValue'));
    }

    public function topProducts(Request $request)
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles) || in_array('attendant', $userRoles);
        
        // For attendants, force current month date range, ignore filters
        if ($isAttendant) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->toDateString();
        } else {
            $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
            $endDate = $request->get('end_date', now()->toDateString());
        }
        
        $limit = (int) $request->get('limit', 10);
        
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();
        
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDateTime, $endDateTime])
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.cost_price',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_revenue'),
                DB::raw('COUNT(DISTINCT sale_items.sale_id) as times_sold'),
                DB::raw('AVG(sale_items.unit_price) as avg_price')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.cost_price')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
        
        return view('reports.top-products', compact('topProducts', 'startDate', 'endDate', 'limit', 'isAttendant'));
    }

    public function export(Request $request)
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles) || in_array('attendant', $userRoles);
        
        // Attendants cannot export reports
        if ($isAttendant) {
            abort(403, 'You do not have permission to export reports.');
        }
        
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        return Excel::download(new SalesExport($startDate, $endDate), 'sales-report.xlsx');
    }
    
    public function customerDebt(Request $request)
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles) || in_array('attendant', $userRoles);
        
        $query = Customer::where('current_balance', '>', 0);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }
        
        $customers = $query->orderBy('current_balance', 'desc')->paginate(20);
        $totalDebt = Customer::sum('current_balance');
        
        return view('reports.customer-debt', compact('customers', 'totalDebt', 'isAttendant'));
    }
    
    public function supplierBalance(Request $request)
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles) || in_array('attendant', $userRoles);
        
        // Attendants cannot access supplier balance
        if ($isAttendant) {
            abort(403, 'You do not have permission to access supplier balance reports.');
        }
        
        $query = Supplier::where('current_balance', '!=', 0);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }
        
        $suppliers = $query->orderBy('current_balance', 'desc')->paginate(20);
        $totalBalance = Supplier::sum('current_balance');
        
        return view('reports.supplier-balance', compact('suppliers', 'totalBalance'));
    }
    
    public function cashFlow(Request $request)
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles) || in_array('attendant', $userRoles);
        
        // Attendants cannot access cash flow
        if ($isAttendant) {
            abort(403, 'You do not have permission to access cash flow reports.');
        }
        
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();
        
        // Cash Inflows
        $cashSales = Sale::whereBetween('sale_date', [$startDateTime, $endDateTime])
            ->whereIn('payment_method', ['cash', 'mobile_money', 'bank'])
            ->where('payment_status', 'paid')
            ->sum('total');
        
        $customerReceipts = Payment::whereBetween('payment_date', [$startDateTime, $endDateTime])
            ->whereHas('sale', function($q) {
                $q->where('payment_method', 'credit');
            })
            ->sum('amount');
        
        $totalInflow = $cashSales + $customerReceipts;
        
        // Cash Outflows
        $purchases = Purchase::whereBetween('purchase_date', [$startDateTime, $endDateTime])
            ->where('payment_status', 'paid')
            ->sum('total');
        
        $supplierPayments = DB::table('purchase_payments')
            ->whereBetween('payment_date', [$startDateTime, $endDateTime])
            ->sum('amount');
        
        $expenses = Expense::whereBetween('expense_date', [$startDateTime, $endDateTime])
            ->sum('amount');
        
        $totalOutflow = $purchases + $supplierPayments + $expenses;
        
        $netCash = $totalInflow - $totalOutflow;
        
        // Calculate Opening Balance
        $previousCashSales = Sale::where('sale_date', '<', $startDateTime)
            ->whereIn('payment_method', ['cash', 'mobile_money', 'bank'])
            ->where('payment_status', 'paid')
            ->sum('total');
        
        $previousCustomerReceipts = Payment::where('payment_date', '<', $startDateTime)
            ->whereHas('sale', function($q) {
                $q->where('payment_method', 'credit');
            })
            ->sum('amount');
        
        $previousTotalInflow = $previousCashSales + $previousCustomerReceipts;
        
        $previousPurchases = Purchase::where('purchase_date', '<', $startDateTime)
            ->where('payment_status', 'paid')
            ->sum('total');
        
        $previousSupplierPayments = DB::table('purchase_payments')
            ->where('payment_date', '<', $startDateTime)
            ->sum('amount');
        
        $previousExpenses = Expense::where('expense_date', '<', $startDateTime)
            ->sum('amount');
        
        $previousTotalOutflow = $previousPurchases + $previousSupplierPayments + $previousExpenses;
        
        $initialBalance = 0;
        $openingBalance = $initialBalance + ($previousTotalInflow - $previousTotalOutflow);
        
        $closingBalance = $openingBalance + $netCash;
        
        $cashFlow = [
            'operating_activities' => [
                'cash_sales' => $cashSales,
                'customer_receipts' => $customerReceipts,
                'total_inflow' => $totalInflow,
                'purchases' => $purchases,
                'supplier_payments' => $supplierPayments,
                'expenses' => $expenses,
                'total_outflow' => $totalOutflow,
                'net_cash' => $netCash
            ],
            'summary' => [
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance
            ]
        ];
        
        return view('reports.cash-flow', compact('cashFlow', 'startDate', 'endDate'));
    }
    
    public function expenseReport(Request $request)
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles) || in_array('attendant', $userRoles);
        
        // Attendants cannot access expense reports
        if ($isAttendant) {
            abort(403, 'You do not have permission to access expense reports.');
        }
        
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());
        
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();
        
        $expenses = Expense::with('category')
            ->whereBetween('expense_date', [$startDateTime, $endDateTime])
            ->orderBy('expense_date', 'desc')
            ->paginate(20);
        
        $totalExpenses = Expense::whereBetween('expense_date', [$startDateTime, $endDateTime])->sum('amount');
        
        $expensesByCategory = Expense::whereBetween('expense_date', [$startDateTime, $endDateTime])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();
        
        return view('reports.expense', compact('expenses', 'totalExpenses', 'startDate', 'endDate', 'expensesByCategory'));
    }
    
    public function exportCustomerDebt()
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles) || in_array('attendant', $userRoles);
        
        // Attendants cannot export customer debt
        if ($isAttendant) {
            abort(403, 'You do not have permission to export customer debt reports.');
        }
        
        return Excel::download(new CustomerDebtExport(), 'customer-debt.xlsx');
    }
    
    public function exportSupplierBalance()
    {
        $userRoles = auth()->user()->roles->pluck('name')->toArray();
        $isAttendant = in_array('Attendant', $userRoles) || in_array('attendant', $userRoles);
        
        // Attendants cannot export supplier balance
        if ($isAttendant) {
            abort(403, 'You do not have permission to export supplier balance reports.');
        }
        
        return Excel::download(new SupplierBalanceExport(), 'supplier-balance.xlsx');
    }
}