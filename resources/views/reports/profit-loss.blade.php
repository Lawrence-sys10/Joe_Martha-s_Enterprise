@extends('layouts.app')

@section('title', 'Profit & Loss Report')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Profit & Loss Statement</h2>
                    <p class="text-amber-100 text-sm mt-1">Track your business profitability</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('reports.daily') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Daily Report
                    </a>
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
        <!-- Date Range Filter -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-2 px-6 rounded-lg transition-all">
                        Generate Report
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Profit & Loss Summary - Matching Dashboard Style -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl shadow-xl border border-blue-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600 font-medium">Total Sales</p>
                        <p class="text-2xl font-bold text-blue-700 mt-2">GHS {{ number_format($profitLoss['total_sales'], 2) }}</p>
                        <p class="text-xs text-blue-500 mt-1">Revenue generated</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl shadow-xl border border-orange-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-orange-600 font-medium">Cost of Goods Sold</p>
                        <p class="text-2xl font-bold text-orange-700 mt-2">GHS {{ number_format($profitLoss['total_cost'], 2) }}</p>
                        <p class="text-xs text-orange-500 mt-1">Cost of products sold</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl shadow-xl border border-red-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-red-600 font-medium">Expenses</p>
                        <p class="text-2xl font-bold text-red-700 mt-2">GHS {{ number_format($profitLoss['total_expenses'], 2) }}</p>
                        <p class="text-xs text-red-500 mt-1">Operating expenses</p>
                    </div>
                    <div class="w-12 h-12 bg-red-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profit Calculation - Matching Dashboard Style -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4">
                <h3 class="text-lg font-bold text-white">Profit & Loss Summary</h3>
                <p class="text-amber-100 text-sm mt-1">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600">Total Sales Revenue</span>
                            <span class="text-lg font-bold text-blue-600">GHS {{ number_format($profitLoss['total_sales'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600">Less: Cost of Goods Sold</span>
                            <span class="text-lg font-bold text-orange-600">- GHS {{ number_format($profitLoss['total_cost'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600 font-semibold">Gross Profit</span>
                            <span class="text-xl font-bold text-green-600">GHS {{ number_format($profitLoss['gross_profit'], 2) }}</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600">Gross Profit Margin</span>
                            <span class="text-lg font-bold text-amber-600">{{ number_format($profitLoss['profit_margin'], 1) }}%</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600">Less: Operating Expenses</span>
                            <span class="text-lg font-bold text-red-600">- GHS {{ number_format($profitLoss['total_expenses'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-gray-800 font-bold text-lg">Net Profit</span>
                            <span class="text-2xl font-bold {{ $profitLoss['net_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                GHS {{ number_format($profitLoss['net_profit'], 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Daily Breakdown Chart -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Daily Performance</h3>
                <p class="text-sm text-gray-500 mt-1">Sales trend over the period</p>
            </div>
            <div class="p-6">
                @if($profitLoss['daily_data']->count() > 0)
                <div class="space-y-3">
                    @foreach($profitLoss['daily_data'] as $day)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</span>
                            <span class="font-semibold text-gray-800">GHS {{ number_format($day->daily_sales, 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $maxDaily = $profitLoss['daily_data']->max('daily_sales');
                                $width = $maxDaily > 0 ? ($day->daily_sales / $maxDaily) * 100 : 0;
                            @endphp
                            <div class="bg-gradient-to-r from-amber-500 to-orange-600 h-2 rounded-full" style="width: {{ $width }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>{{ $day->transaction_count }} transactions</span>
                            <span>Tax: GHS {{ number_format($day->daily_tax, 2) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4">No sales data available for this period</p>
                @endif
            </div>
        </div>
        
        <!-- Top Products Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Top Selling Products</h3>
                <p class="text-sm text-gray-500 mt-1">Best performers this period</p>
            </div>
            <div class="p-6">
                @if($profitLoss['top_products']->count() > 0)
                <div class="space-y-4">
                    @foreach($profitLoss['top_products'] as $product)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-gray-700 font-medium">{{ $product->name }}</span>
                            <span class="text-sm font-semibold text-amber-700">{{ $product->total_quantity }} sold</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-amber-200 rounded-full h-1.5">
                                @php
                                    $maxQty = $profitLoss['top_products']->max('total_quantity');
                                    $width = $maxQty > 0 ? ($product->total_quantity / $maxQty) * 100 : 0;
                                @endphp
                                <div class="bg-gradient-to-r from-amber-500 to-orange-600 h-1.5 rounded-full" style="width: {{ $width }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">GHS {{ number_format($product->total_revenue, 2) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4">No product sales data available</p>
                @endif
            </div>
        </div>
        
        <!-- Summary Note -->
        <div class="mt-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Profit = Total Sales - Cost of Goods Sold - Expenses</span>
                <span class="ml-auto">Margin = (Gross Profit / Total Sales) × 100%</span>
            </div>
        </div>
    </div>
</div>
@endsection