<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class CashBalanceService
{
    /**
     * Get current cash balance (simplified)
     */
    public function getCurrentBalance()
    {
        // Get all completed sales (both cash and credit)
        $sales = Sale::where('status', 'completed')->sum('paid_amount');
        
        // Get all purchases
        $purchases = Purchase::where('status', 'completed')->sum('total');
        
        // Get expenses
        $expenses = Expense::sum('amount');
        
        return $sales - $purchases - $expenses;
    }
    
    /**
     * Get cash flow statement for a date range
     */
    public function getCashFlowStatement($startDate, $endDate)
    {
        // Convert to datetime
        $startDateTime = \Carbon\Carbon::parse($startDate)->startOfDay();
        $endDateTime = \Carbon\Carbon::parse($endDate)->endOfDay();
        
        // Opening balance (balance before start date)
        $openingBalance = $this->getBalanceBeforeDate($startDateTime);
        
        // Operating Activities - Cash Inflows (All completed sales in period)
        $cashSales = Sale::whereBetween('sale_date', [$startDateTime, $endDateTime])
            ->where('status', 'completed')
            ->sum('paid_amount');
        
        $customerReceipts = 0; // This can be calculated from payments if you have a payments table
        
        $totalInflow = $cashSales + $customerReceipts;
        
        // Operating Activities - Cash Outflows
        $purchases = Purchase::whereBetween('purchase_date', [$startDateTime, $endDateTime])
            ->where('status', 'completed')
            ->sum('total');
        
        $supplierPayments = 0; // This can be calculated from purchase payments if you have a purchase_payments table
        
        $expenses = Expense::whereBetween('expense_date', [$startDateTime, $endDateTime])
            ->sum('amount');
        
        $totalOutflow = $purchases + $supplierPayments + $expenses;
        
        // Net cash flow
        $netCashFlow = $totalInflow - $totalOutflow;
        $closingBalance = $openingBalance + $netCashFlow;
        
        return [
            'summary' => [
                'opening_balance' => $openingBalance,
                'net_cash_flow' => $netCashFlow,
                'closing_balance' => $closingBalance,
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate
                ]
            ],
            'operating_activities' => [
                'cash_sales' => $cashSales,
                'customer_receipts' => $customerReceipts,
                'total_inflow' => $totalInflow,
                'purchases' => $purchases,
                'supplier_payments' => $supplierPayments,
                'expenses' => $expenses,
                'total_outflow' => $totalOutflow,
                'net_cash' => $netCashFlow
            ]
        ];
    }
    
    /**
     * Get balance before a specific date
     */
    private function getBalanceBeforeDate($dateTime)
    {
        // Get sales before date
        $sales = Sale::where('sale_date', '<', $dateTime)
            ->where('status', 'completed')
            ->sum('paid_amount');
        
        // Get purchases before date
        $purchases = Purchase::where('purchase_date', '<', $dateTime)
            ->where('status', 'completed')
            ->sum('total');
        
        // Get expenses before date
        $expenses = Expense::where('expense_date', '<', $dateTime)
            ->sum('amount');
        
        return $sales - $purchases - $expenses;
    }
}
