<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SupplierBalanceExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Supplier::where('current_balance', '>', 0)
            ->orWhere('current_balance', '<', 0)
            ->orderBy('current_balance', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Supplier Name',
            'Phone',
            'Email',
            'Current Balance',
            'Total Purchases',
            'Total Payments',
            'Payment Terms'
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->name,
            $supplier->phone,
            $supplier->email,
            number_format($supplier->current_balance, 2),
            number_format($supplier->purchases->sum('total'), 2),
            number_format($supplier->payments->sum('amount'), 2),
            $supplier->payment_terms . ' days'
        ];
    }
}