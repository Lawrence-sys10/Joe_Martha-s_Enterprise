<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getSalesSummary($startDate, $endDate)
    {
        return Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', Sale::STATUS_COMPLETED)
            ->select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('COUNT(*) as total_sales'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('SUM(tax) as total_tax'),
                DB::raw('SUM(discount) as total_discount')
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getProfitLoss($startDate, $endDate)
    {
        $totalSales = Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', Sale::STATUS_COMPLETED)
            ->sum('total');
        
        $totalPurchases = Purchase::whereBetween('purchase_date', [$startDate, $endDate])
            ->where('status', Purchase::STATUS_COMPLETED)
            ->sum('total');
        
        $totalExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
        
        $grossProfit = $totalSales - $totalPurchases;
        $netProfit = $grossProfit - $totalExpenses;
        
        return [
            'total_sales' => $totalSales,
            'total_purchases' => $totalPurchases,
            'total_expenses' => $totalExpenses,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'profit_margin' => $totalSales > 0 ? ($grossProfit / $totalSales) * 100 : 0,
        ];
    }

    public function getTopProducts($startDate, $endDate, $limit = 10)
    {
        return DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->where('sales.status', Sale::STATUS_COMPLETED)
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }
}