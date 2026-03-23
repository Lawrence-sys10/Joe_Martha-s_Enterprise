@extends('layouts.app')

@section('title', 'Payment History')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Payment History</h2>
                    <p class="text-amber-100 text-sm mt-1">Track all supplier payments</p>
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
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Payments</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalPayments }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Amount Paid</p>
                        <p class="text-2xl font-bold text-green-600">GHS {{ number_format($totalAmount, 2) }}</p>
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
                        <p class="text-sm text-gray-500">Suppliers Paid</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $suppliersPaid }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Average Payment</p>
                        <p class="text-2xl font-bold text-amber-600">GHS {{ number_format($totalPayments > 0 ? $totalAmount / $totalPayments : 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                    <select name="supplier_id" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('payments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Payment Methods Breakdown -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                <p class="text-sm text-green-600">Cash</p>
                <p class="text-xl font-bold text-green-700">GHS {{ number_format($paymentMethods['cash'], 2) }}</p>
            </div>
            <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                <p class="text-sm text-blue-600">Bank Transfer</p>
                <p class="text-xl font-bold text-blue-700">GHS {{ number_format($paymentMethods['bank_transfer'], 2) }}</p>
            </div>
            <div class="bg-purple-50 rounded-xl p-4 border border-purple-200">
                <p class="text-sm text-purple-600">Mobile Money</p>
                <p class="text-xl font-bold text-purple-700">GHS {{ number_format($paymentMethods['mobile_money'], 2) }}</p>
            </div>
            <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
                <p class="text-sm text-yellow-600">Cheque</p>
                <p class="text-xl font-bold text-yellow-700">GHS {{ number_format($paymentMethods['cheque'], 2) }}</p>
            </div>
        </div>
        
        <!-- Payments Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-amber-50 to-orange-50">
                        应用
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase">Payment #</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase">Supplier</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase">Purchase Order</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-amber-700 uppercase">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase">Method</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-amber-700 uppercase">Reference</th>
                            <th class="px-6 py-4 text-center text-xs font-medium text-amber-700 uppercase">Actions</th>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payments as $payment)
                        <tr class="hover:bg-amber-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-mono font-bold text-gray-900">{{ $payment->payment_number }}</div>
                              </tr>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $payment->payment_date->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-500">{{ $payment->payment_date->format('H:i:s') }}</div>
                              </tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $payment->purchase->supplier->name }}</div>
                              </tr>
                            <td class="px-6 py-4">
                                <a href="{{ route('purchases.show', $payment->purchase) }}" class="text-sm text-amber-600 hover:text-amber-800">
                                    {{ $payment->purchase->invoice_number }}
                                </a>
                              </tr>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-green-600">GHS {{ number_format($payment->amount, 2) }}</span>
                              </tr>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $payment->payment_method == 'cash' ? 'bg-green-100 text-green-800' : 
                                       ($payment->payment_method == 'bank_transfer' ? 'bg-blue-100 text-blue-800' : 
                                       ($payment->payment_method == 'mobile_money' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </span>
                              </tr>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->reference_number ?? '-' }}</td>
                            <td class="px-6 py-4 text-center text-sm font-medium">
                                <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900 mr-2" title="View Details">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('payments.receipt', $payment) }}" target="_blank" class="text-green-600 hover:text-green-900" title="Print Receipt">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                </a>
                              </tr>
                          </tr>
                        @empty
                          <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-500">No payments recorded yet</p>
                                <p class="text-sm text-gray-400 mt-1">Start recording supplier payments to see them here</p>
                              </td>
                          </tr>
                        @endforelse
                    </tbody>
                  </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $payments->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection