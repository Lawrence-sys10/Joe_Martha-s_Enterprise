@extends('layouts.app')

@section('title', 'Daily Sales Report')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Daily Sales Report</h2>
                    <p class="text-amber-100 text-sm mt-1">View sales performance by day</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('dashboard') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Date Filter -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 mb-6">
            <form method="GET" class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                    <input type="date" name="date" value="{{ $date }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <button type="submit" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-2 px-6 rounded-lg transition-all">
                        View Report
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Report Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Sales</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $summary->first()->total_sales ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Revenue</p>
                        <p class="text-2xl font-bold text-green-600">GHS {{ number_format($summary->first()->total_revenue ?? 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Average Sale</p>
                        <p class="text-2xl font-bold text-purple-600">GHS {{ number_format($summary->first()->average_sale ?? 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sales Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-amber-50 to-orange-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase">Invoice #</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase">Time</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase">Customer</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-amber-700 uppercase">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase">Payment</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-amber-700 uppercase">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-amber-700 uppercase">Actions</th>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sales as $sale)
                        <tr class="hover:bg-amber-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-mono font-bold text-gray-900">{{ $sale->invoice_number }}</div>
                                <div class="text-xs text-gray-500">{{ $sale->items->count() }} items</div>
                             </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $sale->sale_date->format('H:i:s') }}</div>
                             </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $sale->customer->name ?? 'Walk-in Customer' }}</div>
                             </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-bold text-gray-900">GHS {{ number_format($sale->total, 2) }}</div>
                             </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</span>
                             </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ ucfirst($sale->status) }}
                                </span>
                             </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <a href="{{ route('sales.show', $sale) }}" class="text-amber-600 hover:text-amber-900">View</a>
                                <a href="{{ route('sales.receipt', $sale) }}" class="text-green-600 hover:text-green-900 ml-3" target="_blank">Receipt</a>
                             </td>
                         </tr>
                        @empty
                         <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500">No sales found for {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</p>
                                <p class="text-sm text-gray-400 mt-1">Try selecting a different date</p>
                             </td>
                         </tr>
                        @endforelse
                    </tbody>
                 </table>
            </div>
        </div>
    </div>
</div>
@endsection