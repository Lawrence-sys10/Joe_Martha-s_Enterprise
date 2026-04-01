@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Purchase Orders</h2>
                    <p class="text-amber-100 text-sm mt-1">Manage all your purchase orders</p>
                </div>
                <a href="{{ route('suppliers.index') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                    Back to Suppliers
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 mb-6">
            <div class="filter-header mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Filter Purchase Orders</h3>
            </div>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                    <select name="supplier_id" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers ?? [] as $supp)
                        <option value="{{ $supp->id }}" {{ request('supplier_id') == $supp->id ? 'selected' : '' }}>
                            {{ $supp->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="flex gap-2 items-end">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Apply Filters
                    </button>
                    <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-all">
                        Reset
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-2xl shadow-lg border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Purchases</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ $purchases->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Spent</p>
                        <p class="text-2xl font-bold text-green-600 mt-2">GHS {{ number_format($purchases->sum('total'), 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pending Payments</p>
                        <p class="text-2xl font-bold text-red-600 mt-2">GHS {{ number_format($purchases->where('payment_status', '!=', 'paid')->sum('total'), 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Unique Suppliers</p>
                        <p class="text-2xl font-bold text-purple-600 mt-2">{{ $purchases->pluck('supplier_id')->unique()->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Purchases Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Purchase Orders</h3>
                        <p class="text-sm text-gray-500 mt-1">All your purchase transactions</p>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        32
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($purchases as $purchase)
                        @php
                            $paidAmount = $purchase->payments()->sum('amount');
                            $balance = $purchase->total - $paidAmount;
                        @endphp
                        <tr class="hover:bg-amber-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-mono font-bold text-gray-900">{{ $purchase->invoice_number }}</div>
                                <div class="text-xs text-gray-500">{{ $purchase->items->count() }} items</div>
                            </div>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('h:i A') }}</div>
                            </div>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">{{ strtoupper(substr($purchase->supplier->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $purchase->supplier->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $purchase->supplier->phone ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-bold text-gray-900">GHS {{ number_format($purchase->total, 2) }}</div>
                                <div class="text-xs text-gray-500">Paid: GHS {{ number_format($paidAmount, 2) }}</div>
                            </div>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $purchase->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($purchase->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </div>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $purchase->payment_status == 'paid' ? 'bg-green-100 text-green-800' : 
                                       ($purchase->payment_status == 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($purchase->payment_status) }}
                                </span>
                                @if($balance > 0)
                                <div class="text-xs text-red-500 mt-1">Balance: GHS {{ number_format($balance, 2) }}</div>
                                @endif
                            </div>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('purchases.show', $purchase) }}" class="text-amber-600 hover:text-amber-800" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @if($purchase->status == 'pending')
                                    <a href="{{ route('purchases.edit', $purchase) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        32
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500">No purchase orders found</p>
                                <p class="text-sm text-gray-400 mt-1">Create your first purchase order from a supplier</p>
                                <a href="{{ route('suppliers.index') }}" class="inline-block mt-4 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-lg">
                                    Go to Suppliers
                                </a>
                            </div>
                        </div>
                        @endforelse
                    </tbody>
                 </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $purchases->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection