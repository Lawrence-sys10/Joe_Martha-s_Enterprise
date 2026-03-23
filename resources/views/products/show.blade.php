@extends('layouts.app')

@section('title', 'Product Details')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Product Details: ' . $product->name) }}
        </h2>
        <div class="flex gap-3">
            <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                Back to Products
            </a>
            @can('edit products')
            <a href="{{ route('products.edit', $product) }}" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-lg">
                Edit Product
            </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm border border-amber-100 overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Product Image -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg p-6 flex items-center justify-center">
                        <div class="text-center">
                            <svg class="w-32 h-32 text-amber-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p class="text-sm text-amber-400 mt-2">Product Image</p>
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                            @if($product->barcode)
                            <p class="text-xs text-gray-400">Barcode: {{ $product->barcode }}</p>
                            @endif
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Unit Price</p>
                                <p class="text-3xl font-bold text-amber-600">GHS {{ number_format($product->unit_price, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Cost Price</p>
                                <p class="text-lg text-gray-600">GHS {{ number_format($product->cost_price, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Profit Margin</p>
                                <p class="text-lg text-green-600">{{ number_format((($product->unit_price - $product->cost_price) / $product->unit_price) * 100, 1) }}%</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                            <div>
                                <p class="text-sm text-gray-600">Current Stock</p>
                                <p class="text-2xl font-bold {{ $product->isLowStock() ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $product->stock_quantity }} {{ $product->unit }}s
                                </p>
                                @if($product->minimum_stock > 0)
                                <p class="text-xs text-gray-500">Minimum: {{ $product->minimum_stock }}</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Category</p>
                                <p class="text-lg">{{ $product->category->name ?? 'Uncategorized' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Tax Rate</p>
                                <p class="text-lg">{{ $product->tax_rate }}%</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        
                        @if($product->description)
                        <div class="pt-4 border-t">
                            <p class="text-sm text-gray-600 mb-2">Description</p>
                            <p class="text-gray-700">{{ $product->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Stock Movement History -->
                <div class="mt-8 pt-6 border-t">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Stock Movement History</h3>
                    @if($product->stockMovements && $product->stockMovements->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($product->stockMovements->sortByDesc('created_at') as $movement)
                                <tr>
                                    <td class="px-4 py-2 text-sm">{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $movement->type == 'purchase' ? 'bg-green-100 text-green-800' : 
                                               ($movement->type == 'sale' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($movement->type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-right {{ $movement->type == 'sale' ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $movement->type == 'sale' ? '-' : '+' }}{{ $movement->quantity }}
                                    </td>
                                    <td class="px-4 py-2 text-sm">{{ $movement->notes }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $movement->user->name ?? 'System' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-gray-500">No stock movements recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection