@extends('layouts.app')

@section('title', 'Top Selling Products')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Top Selling Products</h2>
                    <p class="text-amber-100 text-sm mt-1">Best performing products by sales volume</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('reports.daily') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Back to Reports
                    </a>
                    <a href="{{ route('reports.daily') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Export Excel
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
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->toDateString()) }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date', now()->toDateString()) }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Limit</label>
                    <select name="limit" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        <option value="10" {{ request('limit', 10) == 10 ? 'selected' : '' }}>Top 10</option>
                        <option value="20" {{ request('limit', 10) == 20 ? 'selected' : '' }}>Top 20</option>
                        <option value="50" {{ request('limit', 10) == 50 ? 'selected' : '' }}>Top 50</option>
                        <option value="100" {{ request('limit', 10) == 100 ? 'selected' : '' }}>Top 100</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-2 px-6 rounded-lg transition-all">
                        Generate Report
                    </button>
                </div>
            </form>
        </div>
        
        @php
            $startDate = request('start_date', now()->startOfMonth()->toDateString());
            $endDate = request('end_date', now()->toDateString());
            $limit = request('limit', 10);
            
            $topProducts = \DB::table('sale_items')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->whereBetween('sales.sale_date', [$startDate, $endDate])
                ->where('sales.status', 'completed')
                ->select(
                    'products.id',
                    'products.name',
                    'products.sku',
                    'products.unit_price',
                    'products.cost_price',
                    \DB::raw('SUM(sale_items.quantity) as total_quantity'),
                    \DB::raw('SUM(sale_items.total) as total_revenue'),
                    \DB::raw('COUNT(DISTINCT sale_items.sale_id) as times_sold'),
                    \DB::raw('AVG(sale_items.unit_price) as avg_price')
                )
                ->groupBy('products.id', 'products.name', 'products.sku', 'products.unit_price', 'products.cost_price')
                ->orderBy('total_revenue', 'desc')
                ->limit($limit)
                ->get();
            
            $totalRevenue = $topProducts->sum('total_revenue');
            $totalQuantity = $topProducts->sum('total_quantity');
        @endphp
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Period</p>
                        <p class="text-lg font-bold text-gray-800 mt-1">
                            {{ \Carbon\Carbon::parse($startDate)->format('M d') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Revenue</p>
                        <p class="text-2xl font-bold text-green-600 mt-2">GHS {{ number_format($totalRevenue, 2) }}</p>
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
                        <p class="text-sm text-gray-500">Total Units Sold</p>
                        <p class="text-2xl font-bold text-purple-600 mt-2">{{ number_format($totalQuantity) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Average Order Value</p>
                        <p class="text-2xl font-bold text-amber-600 mt-2">GHS {{ number_format($totalQuantity > 0 ? $totalRevenue / $totalQuantity : 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Products Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Top {{ $limit }} Selling Products</h3>
                        <p class="text-sm text-gray-500 mt-1">Ranked by revenue generated</p>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Units Sold</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Times Sold</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Avg Price</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Profit</th>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topProducts as $index => $product)
                        @php
                            $profit = $product->total_revenue - ($product->cost_price * $product->total_quantity);
                            $profitMargin = $product->total_revenue > 0 ? ($profit / $product->total_revenue) * 100 : 0;
                        @endphp
                        <tr class="hover:bg-amber-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($index == 0)
                                        <span class="text-2xl">🏆</span>
                                        <span class="ml-2 text-lg font-bold text-amber-600">#{{ $index + 1 }}</span>
                                    @elseif($index == 1)
                                        <span class="text-2xl">🥈</span>
                                        <span class="ml-2 text-lg font-bold text-gray-600">#{{ $index + 1 }}</span>
                                    @elseif($index == 2)
                                        <span class="text-2xl">🥉</span>
                                        <span class="ml-2 text-lg font-bold text-gray-600">#{{ $index + 1 }}</span>
                                    @else
                                        <span class="text-lg font-bold text-gray-500 ml-2">#{{ $index + 1 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">SKU: {{ $product->sku }}</div>
                             </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($product->total_quantity) }}</span>
                             </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-green-600">GHS {{ number_format($product->total_revenue, 2) }}</span>
                                <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                    <div class="bg-green-500 h-1 rounded-full" style="width: {{ ($product->total_revenue / $topProducts->first()->total_revenue) * 100 }}%"></div>
                                </div>
                             </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-600">{{ $product->times_sold }}x</span>
                             </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm text-gray-600">GHS {{ number_format($product->avg_price, 2) }}</span>
                             </td>
                            <td class="px-6 py-4 text-right">
                                <div>
                                    <span class="text-sm font-semibold text-green-600">GHS {{ number_format($profit, 2) }}</span>
                                    <div class="text-xs text-gray-500">({{ number_format($profitMargin, 1) }}%)</div>
                                </div>
                             </td>
                         </tr>
                        @empty
                         <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p class="text-gray-500">No sales data found for this period</p>
                                <p class="text-sm text-gray-400 mt-1">Try selecting a different date range</p>
                            </td>
                         </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr class="font-semibold">
                            <td colspan="2" class="px-6 py-4 text-right">Totals: <span class="text-xs text-gray-500">(Top {{ $limit }})</span></td>
                            <td class="px-6 py-4 text-right">{{ number_format($totalQuantity) }}</td>
                            <td class="px-6 py-4 text-right text-green-700">GHS {{ number_format($totalRevenue, 2) }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                 </table>
            </div>
        </div>
        
        <!-- Insights Section -->
        @if($topProducts->count() > 0)
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h4 class="text-lg font-semibold text-blue-800">Top Performer</h4>
                </div>
                <p class="text-blue-700">
                    <strong>{{ $topProducts->first()->name }}</strong> is your best seller with 
                    <strong>{{ number_format($topProducts->first()->total_revenue, 2) }} GHS</strong> in revenue 
                    ({{ number_format(($topProducts->first()->total_revenue / $totalRevenue) * 100, 1) }}% of total)
                </p>
            </div>
            
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-5 border border-purple-200">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    <h4 class="text-lg font-semibold text-purple-800">Performance Summary</h4>
                </div>
                <div class="space-y-2 text-purple-700">
                    <p>📊 Top {{ $limit }} products account for <strong>{{ number_format(($totalRevenue / \App\Models\Sale::whereBetween('sale_date', [$startDate, $endDate])->where('status', 'completed')->sum('total')) * 100, 1) }}%</strong> of total sales</p>
                    <p>💰 Average profit margin: <strong>{{ number_format($topProducts->avg(function($p) { return ($p->total_revenue - ($p->cost_price * $p->total_quantity)) / $p->total_revenue * 100; }), 1) }}%</strong></p>
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
                <span>Revenue is calculated from completed sales only. Profit = Revenue - (Units Sold × Cost Price)</span>
            </div>
        </div>
    </div>
</div>
@endsection