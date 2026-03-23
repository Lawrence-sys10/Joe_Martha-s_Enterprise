<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\CustomerPayment;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;

class CashBalanceService
{
    public function getCurrentBalance()
    {
        // Cash Inflows
        $cashSales = Sale::where('payment_method', 'cash')
            ->where('status', 'completed')
            ->sum('paid_amount');
            
        $mobileSales = Sale::where('payment_method', 'mobile_money')
            ->where('status', 'completed')
            ->sum('paid_amount');
            
        $customerPayments = CustomerPayment::sum('amount');
        
        // Cash Outflows - Purchases don't have payment_method, so we use all purchases
        $totalPurchases = Purchase::where('status', 'completed')
            ->sum('total');
            
        $supplierPayments = SupplierPayment::sum('amount');
        $expenses = Expense::sum('amount');
        
        $totalInflow = $cashSales + $mobileSales + $customerPayments;
        $totalOutflow = $totalPurchases + $supplierPayments + $expenses;
        
        return [
            'balance' => $totalInflow - $totalOutflow,
            'cash_balance' => $cashSales,
            'mobile_balance' => $mobileSales,
            'inflow' => [
                'sales' => $cashSales + $mobileSales,
                'customer_payments' => $customerPayments,
                'total' => $totalInflow
            ],
            'outflow' => [
                'purchases' => $totalPurchases,
                'supplier_payments' => $supplierPayments,
                'expenses' => $expenses,
                'total' => $totalOutflow
            ]
        ];
    }
    
    public function getCashFlowStatement($startDate, $endDate)
    {
        // Operating Activities
        $cashSales = Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->where('payment_method', 'cash')
            ->where('status', 'completed')
            ->sum('paid_amount');
            
        $customerReceipts = CustomerPayment::whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');
            
        $totalPurchases = Purchase::whereBetween('purchase_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('total');
            
        $supplierPayments = SupplierPayment::whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');
            
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
        
        $netOperatingCash = ($cashSales + $customerReceipts) - ($totalPurchases + $supplierPayments + $expenses);
        
        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'operating_activities' => [
                'cash_sales' => $cashSales,
                'customer_receipts' => $customerReceipts,
                'total_inflow' => $cashSales + $customerReceipts,
                'purchases' => $totalPurchases,
                'supplier_payments' => $supplierPayments,
                'expenses' => $expenses,
                'total_outflow' => $totalPurchases + $supplierPayments + $expenses,
                'net_cash' => $netOperatingCash
            ],
            'summary' => [
                'net_cash_flow' => $netOperatingCash,
                'opening_balance' => $this->getBalanceAtDate($startDate),
                'closing_balance' => $this->getBalanceAtDate($endDate)
            ]
        ];
    }
    
    private function getBalanceAtDate($date)
    {
        // Calculate balance up to a specific date
        $sales = Sale::whereDate('sale_date', '<=', $date)
            ->where('status', 'completed')
            ->sum('paid_amount');
            
        $purchases = Purchase::whereDate('purchase_date', '<=', $date)
            ->where('status', 'completed')
            ->sum('total');
            
        $expenses = Expense::whereDate('expense_date', '<=', $date)
            ->sum('amount');
            
        return $sales - $purchases - $expenses;
    }
}