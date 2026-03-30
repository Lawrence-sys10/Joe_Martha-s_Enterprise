@extends('layouts.app')

@section('title', 'Expense Details')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Expense Details</h2>
                    <p class="text-amber-100 text-sm mt-1">{{ $expense->invoice_number }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('expenses.edit', $expense) }}" class="bg-white hover:bg-amber-50 text-amber-600 font-bold py-2 px-4 rounded-lg shadow-md transition-all">
                        Edit Expense
                    </a>
                    <a href="{{ route('expenses.index') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Back to Expenses
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Invoice Number</p>
                        <p class="text-lg font-bold text-gray-800">{{ $expense->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="text-lg font-bold text-gray-800">{{ \Carbon\Carbon::parse($expense->expense_date)->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Category</p>
                        <p class="text-lg font-bold text-gray-800">{{ $expense->category }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Amount</p>
                        <p class="text-2xl font-bold text-red-600">GHS {{ number_format($expense->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Payment Method</p>
                        <p class="text-lg font-bold text-gray-800">{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Recorded By</p>
                        <p class="text-lg font-bold text-gray-800">{{ $expense->user->name ?? 'System' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Description</p>
                        <p class="text-gray-800">{{ $expense->description }}</p>
                    </div>
                    @if($expense->notes)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Notes</p>
                        <p class="text-gray-800">{{ $expense->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
