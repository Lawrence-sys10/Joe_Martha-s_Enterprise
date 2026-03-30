@extends('layouts.app')

@section('title', 'Stock Valuation Report')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Stock Valuation Report</h2>
                    <p class="text-amber-100 text-sm mt-1">Current inventory value and stock levels</p>
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
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Products</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ \App\Models\Product::count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Stock Value</p>
                        <p class="text-2xl font-bold text-green-600 mt-2">GHS {{ number_format($totalValue, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Average Stock Value</p>
                        <p class="text-2xl font-bold text-purple-600 mt-2">
                            GHS {{ number_format($products->count() > 0 ? $totalValue / $products->count() : 0, 2) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stock Valuation Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Inventory Details</h3>
                        <p class="text-sm text-gray-500 mt-1">Current stock levels and values</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Active Products</span>
                        <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Low Stock</span>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cost Price</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Value</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                        @php
                            // Calculate total value for this product - ensure values are numeric
                            $stockQty = floatval($product->stock_quantity ?? 0);
                            $costPrice = floatval($product->cost_price ?? 0);
                            $itemTotalValue = $stockQty * $costPrice;
                            $isLowStock = $stockQty <= floatval($product->minimum_stock ?? 0) && $stockQty > 0;
                            $isOutOfStock = $stockQty <= 0;
                        @endphp
                        <tr class="hover:bg-amber-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">{{ $product->unit ?? 'piece' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 font-mono">{{ $product->sku ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-semibold {{ $isLowStock ? 'text-yellow-600' : ($isOutOfStock ? 'text-red-600' : 'text-gray-900') }}">
                                    {{ number_format($stockQty) }}
                                </span>
                                @if(($product->minimum_stock ?? 0) > 0 && !$isOutOfStock)
                                <div class="text-xs text-gray-400">Min: {{ number_format($product->minimum_stock) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-medium text-gray-900">GHS {{ number_format($costPrice, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-green-600">GHS {{ number_format($itemTotalValue, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($isOutOfStock)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Out of Stock
                                </span>
                                @elseif($isLowStock)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Low Stock
                                </span>
                                @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    In Stock
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p class="text-gray-500">No products found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr class="font-semibold">
                            <td colspan="4" class="px-6 py-4 text-right">Total Stock Value:</td>
                            <td class="px-6 py-4 text-right text-lg font-bold text-amber-600">GHS {{ number_format($totalValue, 2) }}</td>
                            <td class="px-6 py-4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Low Stock Alert Section -->
        @php
            $lowStockItems = $products->filter(function($p) {
                $stockQty = floatval($p->stock_quantity ?? 0);
                $minStock = floatval($p->minimum_stock ?? 0);
                return $stockQty <= $minStock && $stockQty > 0;
            });
            $outOfStockItems = $products->filter(function($p) {
                return floatval($p->stock_quantity ?? 0) <= 0;
            });
        @endphp
        
        @if($lowStockItems->count() > 0 || $outOfStockItems->count() > 0)
        <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg shadow-md overflow-hidden">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-yellow-500 rounded-full p-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-yellow-800">⚠️ Stock Alert</h3>
                            <p class="text-sm text-yellow-700">
                                {{ $outOfStockItems->count() }} out of stock, 
                                {{ $lowStockItems->count() }} low stock items need attention
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('products.index', ['low_stock' => 1]) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all">
                        View Products
                    </a>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Summary Note -->
        <div class="mt-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Stock Value = Quantity × Cost Price</span>
                <span class="ml-auto">Total inventory investment: GHS {{ number_format($totalValue, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection