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
                    @php
                        $userRoles = Auth::user()->roles->pluck('name')->toArray();
                        $isAttendant = in_array('Attendant', $userRoles) || in_array('attendant', $userRoles);
                    @endphp
                    
                    @if(!$isAttendant)
                    <a href="{{ route('reports.monthly') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Monthly Report
                    </a>
                    @endif
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
        @php
            $userRoles = Auth::user()->roles->pluck('name')->toArray();
            $isAttendant = in_array('Attendant', $userRoles) || in_array('attendant', $userRoles);
        @endphp
        
        <!-- Date Filter - Hidden for Attendants -->
        @if(!$isAttendant)
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
        @else
        <!-- Info message for attendants -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-800">Today's Sales Report</p>
                    <p class="text-xs text-blue-600">Showing sales for {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}. Date filtering is not available for your role.</p>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Report Summary - Matching sales index cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-amber-600 font-medium">Total Sales</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ $summary->first()->total_sales ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">Number of transactions</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-amber-600 font-medium">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">GHS {{ number_format($summary->first()->total_revenue ?? 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">Total sales amount</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-amber-600 font-medium">Average Sale</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">GHS {{ number_format($summary->first()->average_sale ?? 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">Average per transaction</div>
            </div>
        </div>
        
        <!-- Sales Table - Matching sales index columns -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-amber-50 to-orange-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase tracking-wider">Invoice</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-amber-700 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase tracking-wider">Payment</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-amber-700 uppercase tracking-wider">Payment Status</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-amber-700 uppercase tracking-wider">Actions</th>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sales as $sale)
                        <tr class="hover:bg-amber-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-mono font-bold text-gray-900">{{ $sale->invoice_number }}</div>
                                <div class="text-xs text-gray-500">{{ $sale->items->count() }} items</div>
                               </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $sale->sale_date->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-500">{{ $sale->sale_date->format('H:i:s') }}</div>
                               </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $sale->customer->name ?? 'Walk-in Customer' }}</div>
                                @if($sale->customer)
                                <div class="text-xs text-gray-500">{{ $sale->customer->phone ?? '' }}</div>
                                @endif
                               </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-bold text-gray-900">GHS {{ number_format($sale->total, 2) }}</div>
                                @if($sale->discount > 0)
                                <div class="text-xs text-green-600">-{{ number_format($sale->discount, 2) }} discount</div>
                                @endif
                               </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($sale->payment_method == 'cash')
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif($sale->payment_method == 'mobile_money')
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    @elseif($sale->payment_method == 'credit')
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    @endif
                                    <span class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</span>
                                </div>
                               </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $sale->payment_status == 'paid' ? 'bg-green-100 text-green-800' : 
                                       ($sale->payment_status == 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($sale->payment_status) }}
                                </span>
                                @if($sale->payment_status == 'partial')
                                <div class="text-xs text-gray-500 mt-1">
                                    Balance: GHS {{ number_format($sale->total - $sale->payments()->sum('amount'), 2) }}
                                </div>
                                @endif
                               </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('sales.show', $sale) }}" class="text-amber-600 hover:text-amber-900" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @if($sale->payment_status != 'pending')
                                    <a href="{{ route('sales.receipt', $sale) }}" class="text-green-600 hover:text-green-900" title="Print Receipt" target="_blank">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                    </a>
                                    @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Receipt available only after payment">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                    </span>
                                    @endif
                                </div>
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
                                @if($isAttendant)
                                <p class="text-xs text-amber-600 mt-2">Note: Attendants can only view today's sales. Please contact an administrator to view other dates.</p>
                                @endif
                               </td>
                           </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination (only for non-attendants if needed) -->
        @if(isset($sales) && method_exists($sales, 'links') && !$isAttendant)
        <div class="mt-4">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>
@endsection