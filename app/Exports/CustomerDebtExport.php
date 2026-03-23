<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerDebtExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Customer::where('current_balance', '>', 0)
            ->orderBy('current_balance', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Phone',
            'Email',
            'Current Balance',
            'Total Purchases',
            'Total Payments',
            'Last Purchase Date'
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->name,
            $customer->phone,
            $customer->email,
            number_format($customer->current_balance, 2),
            number_format($customer->sales->sum('total'), 2),
            number_format($customer->transactions->sum('amount'), 2),
            $customer->sales->max('created_at')?->format('Y-m-d') ?? 'No purchases'
        ];
    }
}