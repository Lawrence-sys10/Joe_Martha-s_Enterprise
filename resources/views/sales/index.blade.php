@extends('layouts.app')

@section('title', 'Sales Transactions')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Sales Transactions</h2>
                    <p class="text-amber-100 text-sm mt-1">Manage and track all sales activities</p>
                </div>
                @can('access pos')
                <a href="{{ route('pos.index') }}" class="bg-white hover:bg-amber-50 text-amber-600 font-bold py-2 px-4 rounded-lg shadow-md transition-all transform hover:scale-105">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Sale
                </a>
                @endcan
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @php
            $userRoles = Auth::user()->roles->pluck('name')->toArray();
            $isAttendant = in_array('Attendant', $userRoles);
        @endphp

        <!-- Stats Cards - Attendants only see today's stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-amber-600 font-medium">{{ $isAttendant ? "Today's" : "Total" }} Sales</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">GHS {{ number_format($isAttendant ? $todaySales : $totalSales, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">{{ $isAttendant ? "Today's revenue" : "All time revenue" }}</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-amber-600 font-medium">Today's Sales</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">GHS {{ number_format($todaySales, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">{{ $todayCount }} transactions today</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-amber-600 font-medium">Avg. Sale Value</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">GHS {{ number_format($avgSaleValue, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">Average per transaction</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-amber-600 font-medium">{{ $isAttendant ? "Today's" : "Total" }} Transactions</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ $isAttendant ? $todayCount : $totalTransactions }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-red-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">{{ $isAttendant ? "Today's orders" : "Total orders processed" }}</div>
            </div>
        </div>
        
        <!-- Credit Sales Summary Cards - Attendants can see partial payments but not full credit summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-2xl shadow-lg border border-amber-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-amber-700 font-medium">Pending Credit</p>
                        <p class="text-2xl font-bold text-amber-800 mt-2">GHS {{ number_format($pendingCredit ?? 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-amber-600">Awaiting payment</div>
            </div>
            
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl shadow-lg border border-blue-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-700 font-medium">Partial Payments</p>
                        <p class="text-2xl font-bold text-blue-800 mt-2">GHS {{ number_format($partialCredit ?? 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-blue-600">Partially paid sales</div>
            </div>
            
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl shadow-lg border border-green-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-700 font-medium">Paid Credit</p>
                        <p class="text-2xl font-bold text-green-800 mt-2">GHS {{ number_format($paidCredit ?? 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-green-600">Fully paid credit sales</div>
            </div>
        </div>
        
        <!-- Filters - Attendants don't need filters since they only see today -->
        @if(!$isAttendant)
        <div class="filter-section">
            <div class="filter-header">
                <h3 class="text-lg font-semibold text-gray-800">Filter Transactions</h3>
                <button onclick="resetFilters()" class="reset-btn">Reset Filters</button>
            </div>
            <form method="GET" class="filter-grid">
                <div>
                    <label class="filter-label">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 filter-input">
                </div>
                <div>
                    <label class="filter-label">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 filter-input">
                </div>
                <div>
                    <label class="filter-label">Payment Method</label>
                    <select name="payment_method" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 filter-select">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="bank" {{ request('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="credit" {{ request('payment_method') == 'credit' ? 'selected' : '' }}>Credit</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label">Payment Status</label>
                    <select name="payment_status" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 filter-select">
                        <option value="">All Payment Status</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full filter-btn text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
        @endif
        
        <!-- Sales Table -->
        <div class="bg-white rounded-2xl shadow-lg border border-amber-100 overflow-hidden">
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
                                <p class="text-gray-500">No sales transactions found for today</p>
                                <p class="text-sm text-gray-400 mt-1">Start selling to see transactions here</p>
                                @can('access pos')
                                <a href="{{ route('pos.index') }}" class="inline-block mt-4 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-lg">
                                    Make First Sale
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(!$isAttendant)
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $sales->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function resetFilters() {
        window.location.href = '{{ route("sales.index") }}';
    }
</script>
@endpush
@endsection