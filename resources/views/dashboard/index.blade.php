@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-white">
                Welcome back, {{ Auth::user()->name }}!
            </h2>
            <p class="text-amber-100 text-sm mt-1">{{ now()->format('l, F j, Y') }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stock Alert Cards -->
        @php
            $lowStockProducts = \App\Models\Product::whereRaw('stock_quantity <= minimum_stock')
                ->where('stock_quantity', '>', 0)
                ->where('is_active', true)
                ->get();
            
            $outOfStockProducts = \App\Models\Product::where('stock_quantity', '<=', 0)
                ->where('is_active', true)
                ->get();
            
            $totalLowStock = $lowStockProducts->count();
            $totalOutOfStock = $outOfStockProducts->count();
        @endphp
        
        <!-- Stock Alert Banner - Red for Out of Stock, Yellow for Low Stock -->
        @if($totalOutOfStock > 0 || $totalLowStock > 0)
        <div class="mb-6 {{ $totalOutOfStock > 0 ? 'bg-red-50 border-l-4 border-red-500' : 'bg-yellow-50 border-l-4 border-yellow-500' }} rounded-lg shadow-md overflow-hidden">
            <div class="p-4">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-3">
                        <div class="{{ $totalOutOfStock > 0 ? 'bg-red-500' : 'bg-yellow-500' }} rounded-full p-2 animate-pulse">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            @if($totalOutOfStock > 0)
                            <h3 class="text-lg font-bold text-red-800">🚫 OUT OF STOCK ALERT!</h3>
                            <p class="text-sm text-red-600">{{ $totalOutOfStock }} product(s) are completely out of stock</p>
                            @endif
                            @if($totalLowStock > 0)
                            <h3 class="text-lg font-bold {{ $totalOutOfStock > 0 ? 'text-red-800 mt-2' : 'text-yellow-800' }}">⚠️ LOW STOCK ALERT!</h3>
                            <p class="text-sm {{ $totalOutOfStock > 0 ? 'text-red-600' : 'text-yellow-600' }}">{{ $totalLowStock }} product(s) are running low on stock</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex gap-2">
                        @if($totalOutOfStock > 0)
                        <a href="{{ route('products.index', ['out_of_stock' => 1]) }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all">
                            View Out of Stock
                        </a>
                        @endif
                        @if($totalLowStock > 0)
                        <a href="{{ route('products.index', ['low_stock' => 1]) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all">
                            View Low Stock
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Out of Stock Items List -->
            @if($totalOutOfStock > 0)
            <div class="bg-white border-t border-red-100">
                <div class="px-4 py-2 bg-red-50 border-b border-red-100">
                    <p class="text-sm font-semibold text-red-700">🚫 Out of Stock Items (Need Immediate Restock)</p>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($outOfStockProducts as $product)
                    <div class="p-4 hover:bg-red-50 transition-colors">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500">SKU: {{ $product->sku }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Current Stock:</p>
                                <p class="text-xl font-bold text-red-600">0 {{ $product->unit }}s</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm transition-colors">
                                    Restock Now
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Low Stock Items List -->
            @if($totalLowStock > 0)
            <div class="bg-white border-t border-yellow-100">
                <div class="px-4 py-2 bg-yellow-50 border-b border-yellow-100">
                    <p class="text-sm font-semibold text-yellow-700">⚠️ Low Stock Items (Order Soon)</p>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($lowStockProducts as $product)
                    <div class="p-4 hover:bg-yellow-50 transition-colors">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500">SKU: {{ $product->sku }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Current Stock:</p>
                                <p class="text-xl font-bold text-yellow-600">{{ $product->stock_quantity }} <span class="text-sm">{{ $product->unit }}s</span></p>
                                <p class="text-xs text-gray-500">Min: {{ $product->minimum_stock }} {{ $product->unit }}s</p>
                            </div>
                            <div class="w-32">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    @php
                                        $percentage = ($product->stock_quantity / $product->minimum_stock) * 100;
                                        $percentage = min($percentage, 100);
                                    @endphp
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ number_format($percentage, 0) }}% of min stock</p>
                            </div>
                            <div>
                                <a href="{{ route('products.edit', $product) }}" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded-lg text-sm transition-colors">
                                    Restock
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif
        
        <!-- Stats Grid - 6 cards for better overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
            <!-- Total Products Card -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-amber-100">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-amber-600 font-medium uppercase tracking-wide">Total Products</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">{{ \App\Models\Product::count() }}</p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Today's Sales Card -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-amber-100">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-amber-600 font-medium uppercase tracking-wide">Today's Sales</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">GHS {{ number_format($todaySales, 2) }}</p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Low Stock Count Card -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-amber-100">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-amber-600 font-medium uppercase tracking-wide">Low Stock</p>
                            <p class="text-2xl font-bold {{ $totalLowStock > 0 ? 'text-yellow-600' : 'text-gray-800' }} mt-1">{{ $totalLowStock }}</p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Out of Stock Count Card -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-amber-100">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-amber-600 font-medium uppercase tracking-wide">Out of Stock</p>
                            <p class="text-2xl font-bold {{ $totalOutOfStock > 0 ? 'text-red-600' : 'text-gray-800' }} mt-1">{{ $totalOutOfStock }}</p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-pink-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 364l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stock Value Card -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-amber-100">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-amber-600 font-medium uppercase tracking-wide">Stock Value</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">GHS {{ number_format(\App\Models\Product::sum(\DB::raw('stock_quantity * cost_price')), 2) }}</p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Supplier Balance Card -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-amber-100">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-amber-600 font-medium uppercase tracking-wide">Supplier Balance</p>
                            <p class="text-2xl font-bold {{ $supplierBalance > 0 ? 'text-red-600' : ($supplierBalance < 0 ? 'text-green-600' : 'text-gray-800') }} mt-1">
                                GHS {{ number_format($supplierBalance, 2) }}
                            </p>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-br from-red-400 to-pink-500 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm0 0v4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profit & Loss Section -->
        <div class="bg-white rounded-xl shadow-lg border border-amber-100 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-white">Profit & Loss Statement</h3>
                        <p class="text-amber-100 text-sm mt-1">Track your business performance</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="showPeriod('day')" id="dayBtn" class="px-3 py-1 bg-white/20 text-white rounded-lg text-sm hover:bg-white/30 transition-all">Today</button>
                        <button onclick="showPeriod('month')" id="monthBtn" class="px-3 py-1 bg-white/20 text-white rounded-lg text-sm hover:bg-white/30 transition-all">This Month</button>
                        <button onclick="showPeriod('year')" id="yearBtn" class="px-3 py-1 bg-white/20 text-white rounded-lg text-sm hover:bg-white/30 transition-all">This Year</button>
                    </div>
                </div>
            </div>
            
            <!-- Day Period -->
            <div id="dayPeriod" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                        <p class="text-sm text-blue-600 font-medium">Total Sales</p>
                        <p class="text-2xl font-bold text-blue-700 mt-2">GHS {{ number_format($todaySales, 2) }}</p>
                        <p class="text-xs text-blue-500 mt-1">Revenue generated today</p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-5">
                        <p class="text-sm text-orange-600 font-medium">Cost of Goods Sold</p>
                        <p class="text-2xl font-bold text-orange-700 mt-2">GHS {{ number_format($todayCost, 2) }}</p>
                        <p class="text-xs text-orange-500 mt-1">Cost of products sold</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                        <p class="text-sm text-green-600 font-medium">Gross Profit</p>
                        <p class="text-2xl font-bold text-green-700 mt-2">GHS {{ number_format($todayProfit, 2) }}</p>
                        <p class="text-xs text-green-500 mt-1">Margin: {{ number_format($todayMargin, 1) }}%</p>
                    </div>
                </div>
            </div>
            
            <!-- Month Period -->
            <div id="monthPeriod" class="p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                        <p class="text-sm text-blue-600 font-medium">Total Sales</p>
                        <p class="text-2xl font-bold text-blue-700 mt-2">GHS {{ number_format($monthlySales, 2) }}</p>
                        <p class="text-xs text-blue-500 mt-1">Revenue this month</p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-5">
                        <p class="text-sm text-orange-600 font-medium">Cost of Goods Sold</p>
                        <p class="text-2xl font-bold text-orange-700 mt-2">GHS {{ number_format($monthlyCost, 2) }}</p>
                        <p class="text-xs text-orange-500 mt-1">Cost of products sold</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                        <p class="text-sm text-green-600 font-medium">Gross Profit</p>
                        <p class="text-2xl font-bold text-green-700 mt-2">GHS {{ number_format($monthlyProfit, 2) }}</p>
                        <p class="text-xs text-green-500 mt-1">Margin: {{ number_format($monthlyMargin, 1) }}%</p>
                    </div>
                </div>
            </div>
            
            <!-- Year Period -->
            <div id="yearPeriod" class="p-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                        <p class="text-sm text-blue-600 font-medium">Total Sales</p>
                        <p class="text-2xl font-bold text-blue-700 mt-2">GHS {{ number_format($yearlySales, 2) }}</p>
                        <p class="text-xs text-blue-500 mt-1">Revenue this year</p>
                    </div>
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-5">
                        <p class="text-sm text-orange-600 font-medium">Cost of Goods Sold</p>
                        <p class="text-2xl font-bold text-orange-700 mt-2">GHS {{ number_format($yearlyCost, 2) }}</p>
                        <p class="text-xs text-orange-500 mt-1">Cost of products sold</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                        <p class="text-sm text-green-600 font-medium">Gross Profit</p>
                        <p class="text-2xl font-bold text-green-700 mt-2">GHS {{ number_format($yearlyProfit, 2) }}</p>
                        <p class="text-xs text-green-500 mt-1">Margin: {{ number_format($yearlyMargin, 1) }}%</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>📊 Profit Margin = (Revenue - Cost) / Revenue × 100%</span>
                    <span>💰 Higher margin means better profitability</span>
                </div>
            </div>
        </div>
        
        <!-- Three Column Layout for Sales, Purchases, and Top Products -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Sales -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-amber-100">
                <div class="p-5 border-b border-amber-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Recent Sales</h3>
                            <p class="text-xs text-amber-600 mt-1">Latest transactions</p>
                        </div>
                        <a href="{{ route('sales.index') }}" class="text-xs text-amber-600 hover:text-amber-800">View All →</a>
                    </div>
                </div>
                <div class="p-5 max-h-96 overflow-y-auto">
                    @php $recentSales = \App\Models\Sale::with('customer')->latest()->limit(5)->get(); @endphp
                    @if($recentSales->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentSales as $sale)
                            <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800 text-sm">{{ $sale->invoice_number }}</p>
                                    <p class="text-xs text-amber-600">{{ $sale->customer->name ?? 'Walk-in Customer' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-800 text-sm">GHS {{ number_format($sale->total, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ $sale->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-amber-600 text-center py-8 text-sm">No sales recorded yet</p>
                    @endif
                </div>
            </div>
            
            <!-- Recent Purchases -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-amber-100">
                <div class="p-5 border-b border-amber-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Recent Purchases</h3>
                            <p class="text-xs text-amber-600 mt-1">Latest orders from suppliers</p>
                        </div>
                        <a href="{{ route('suppliers.index') }}" class="text-xs text-amber-600 hover:text-amber-800">View All →</a>
                    </div>
                </div>
                <div class="p-5 max-h-96 overflow-y-auto">
                    @php $recentPurchases = \App\Models\Purchase::with('supplier')->latest()->limit(5)->get(); @endphp
                    @if($recentPurchases->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentPurchases as $purchase)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800 text-sm">{{ $purchase->invoice_number }}</p>
                                    <p class="text-xs text-amber-600">{{ $purchase->supplier->name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-800 text-sm">GHS {{ number_format($purchase->total, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ $purchase->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-amber-600 text-center py-8 text-sm">No purchases recorded yet</p>
                    @endif
                </div>
            </div>
            
            <!-- Top Selling Products -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-amber-100">
                <div class="p-5 border-b border-amber-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Top Selling Products</h3>
                            <p class="text-xs text-amber-600 mt-1">This month's best sellers</p>
                        </div>
                        <a href="{{ route('products.index') }}" class="text-xs text-amber-600 hover:text-amber-800">View All →</a>
                    </div>
                </div>
                <div class="p-5">
                    @php
                        $topProducts = \DB::table('sale_items')
                            ->join('products', 'sale_items.product_id', '=', 'products.id')
                            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                            ->whereMonth('sales.sale_date', now()->month)
                            ->whereYear('sales.sale_date', now()->year)
                            ->where('sales.status', 'completed')
                            ->select('products.name', \DB::raw('SUM(sale_items.quantity) as total_sold'), \DB::raw('SUM(sale_items.total) as total_revenue'))
                            ->groupBy('products.id', 'products.name')
                            ->orderBy('total_sold', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp
                    @if($topProducts->count() > 0)
                        <div class="space-y-4">
                            @foreach($topProducts as $product)
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-gray-700 text-sm font-medium">{{ $product->name }}</span>
                                    <span class="text-xs font-semibold text-amber-700">{{ $product->total_sold }} sold</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-amber-200 rounded-full h-1.5">
                                        <div class="bg-gradient-to-r from-amber-500 to-orange-600 h-1.5 rounded-full" style="width: {{ ($product->total_sold / $topProducts->first()->total_sold) * 100 }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">GHS {{ number_format($product->total_revenue, 2) }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-amber-600 text-center py-8 text-sm">No sales data available</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="mt-8">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-5 border border-amber-200">
                <h3 class="text-base font-semibold text-gray-800 mb-3">Quick Actions</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                    @can('create products')
                    <a href="{{ route('products.create') }}" class="flex items-center justify-center gap-2 bg-white hover:bg-amber-50 text-amber-700 font-medium py-2 px-3 rounded-lg border border-amber-200 transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Add Product</span>
                    </a>
                    @endcan
                    
                    @can('access pos')
                    <a href="{{ route('pos.index') }}" class="flex items-center justify-center gap-2 bg-white hover:bg-amber-50 text-amber-700 font-medium py-2 px-3 rounded-lg border border-amber-200 transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>New Sale</span>
                    </a>
                    @endcan
                    
                    @can('create customers')
                    <a href="{{ route('customers.create') }}" class="flex items-center justify-center gap-2 bg-white hover:bg-amber-50 text-amber-700 font-medium py-2 px-3 rounded-lg border border-amber-200 transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        <span>Add Customer</span>
                    </a>
                    @endcan
                    
                    @can('view purchases')
                    <a href="{{ route('suppliers.index') }}" class="flex items-center justify-center gap-2 bg-white hover:bg-amber-50 text-amber-700 font-medium py-2 px-3 rounded-lg border border-amber-200 transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span>New Purchase</span>
                    </a>
                    @endcan
                    
                    @can('view reports')
                    <a href="{{ route('reports.daily') }}" class="flex items-center justify-center gap-2 bg-white hover:bg-amber-50 text-amber-700 font-medium py-2 px-3 rounded-lg border border-amber-200 transition-colors text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>View Daily-Reports</span>
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showPeriod(period) {
    document.getElementById('dayPeriod').classList.add('hidden');
    document.getElementById('monthPeriod').classList.add('hidden');
    document.getElementById('yearPeriod').classList.add('hidden');
    document.getElementById(period + 'Period').classList.remove('hidden');
    
    const buttons = ['day', 'month', 'year'];
    buttons.forEach(btn => {
        const element = document.getElementById(btn + 'Btn');
        if (btn === period) {
            element.classList.add('bg-white/30');
            element.classList.remove('bg-white/20');
        } else {
            element.classList.add('bg-white/20');
            element.classList.remove('bg-white/30');
        }
    });
}
</script>
@endsection