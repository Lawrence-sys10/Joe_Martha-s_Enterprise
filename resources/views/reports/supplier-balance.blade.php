@extends('layouts.app')

@section('title', 'Supplier Balance Report')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Supplier Balance Report</h2>
                    <p class="text-amber-100 text-sm mt-1">Amounts owed to suppliers</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('reports.daily') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Back to Reports
                    </a>
                    <a href="{{ route('reports.supplier-balance.export') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition-all">
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
        @php
            $suppliers = \App\Models\Supplier::where('current_balance', '>', 0)
                ->orderBy('current_balance', 'desc')
                ->paginate(20);
            $totalBalance = \App\Models\Supplier::sum('current_balance');
        @endphp
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Suppliers with Balance</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ $suppliers->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Amount Owed</p>
                        <p class="text-2xl font-bold text-red-600 mt-2">GHS {{ number_format($totalBalance, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Average Payable</p>
                        <p class="text-2xl font-bold text-purple-600 mt-2">GHS {{ number_format($suppliers->total() > 0 ? $totalBalance / $suppliers->total() : 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Suppliers Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Supplier Balances</h3>
                <p class="text-sm text-gray-500 mt-1">Amounts currently owed to suppliers</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance Owed</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Purchases</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Payments</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Payment Terms</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($suppliers as $supplier)
                        @php
                            $totalPurchases = $supplier->purchases->sum('total');
                            $totalPayments = $supplier->payments->sum('amount');
                        @endphp
                        <tr class="hover:bg-amber-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ strtoupper(substr($supplier->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $supplier->name }}</div>
                                        <div class="text-xs text-gray-500">Since {{ $supplier->created_at->format('M Y') }}</div>
                                    </div>
                                </div>
                             </td>
                            <td class="px-6 py-4">
                                @if($supplier->phone)
                                <div class="text-sm text-gray-600">📞 {{ $supplier->phone }}</div>
                                @endif
                                @if($supplier->email)
                                <div class="text-xs text-gray-500">✉️ {{ $supplier->email }}</div>
                                @endif
                             </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-red-600">GHS {{ number_format($supplier->current_balance, 2) }}</span>
                                <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                    <div class="bg-red-500 h-1 rounded-full" style="width: {{ min(100, ($supplier->current_balance / $totalBalance) * 100) }}%"></div>
                                </div>
                             </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-600">
                                GHS {{ number_format($totalPurchases, 2) }}
                             </td>
                            <td class="px-6 py-4 text-right text-sm text-green-600">
                                GHS {{ number_format($totalPayments, 2) }}
                             </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">
                                {{ $supplier->payment_terms ?? 30 }} days
                             </td>
                            <td class="px-6 py-4 text-center text-sm font-medium">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('suppliers.purchase.create', $supplier) }}" class="text-green-600 hover:text-green-900 ml-2" title="Create Purchase">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </a>
                              </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-500">No supplier balances</p>
                                <p class="text-sm text-gray-400 mt-1">All suppliers are paid up</p>
                              </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr class="font-semibold">
                            <td colspan="2" class="px-6 py-4 text-right">Totals:</td>
                            <td class="px-6 py-4 text-right text-red-600">GHS {{ number_format($totalBalance, 2) }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $suppliers->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
