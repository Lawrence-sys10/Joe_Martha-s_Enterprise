@extends('layouts.app')

@section('title', 'Supplier Payments Report')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Supplier Payments Report</h2>
                    <p class="text-amber-100 text-sm mt-1">Track all payments made to suppliers</p>
                </div>
                <a href="{{ route('dashboard') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        @endif
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Payments</p>
                        <p class="text-2xl font-bold text-green-600 mt-2">GHS {{ number_format($totalPaymentsAmount, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">Total amount paid</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Transactions</p>
                        <p class="text-2xl font-bold text-blue-600 mt-2">{{ number_format($totalTransactionsCount) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">Number of payments</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Average Payment</p>
                        <p class="text-2xl font-bold text-purple-600 mt-2">GHS {{ number_format($avgPayment, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">Average payment amount</div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Unique Suppliers</p>
                        <p class="text-2xl font-bold text-amber-600 mt-2">{{ $uniqueSuppliers }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-500">Suppliers paid</div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filter-section">
            <div class="filter-header">
                <h3 class="text-lg font-semibold text-gray-800">Filter Payments</h3>
                <button onclick="resetFilters()" class="text-sm text-amber-600 hover:text-amber-800">Reset Filters</button>
            </div>
            <form method="GET" class="filter-grid">
                <div>
                    <label class="filter-label">Supplier</label>
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
                    <label class="filter-label">Payment Method</label>
                    <select name="payment_method" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="filter-label">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div class="filter-actions">
                    <button type="submit" class="filter-btn text-white font-bold py-2 px-6 rounded-lg transition-all">
                        Apply Filters
                    </button>
                    <a href="{{ route('reports.supplier-payments') }}" class="clear-btn text-white font-bold py-2 px-6 rounded-lg transition-all">
                        Clear
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Payments Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Payment Transactions</h3>
                        <p class="text-sm text-gray-500 mt-1">All supplier payment records</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Total: {{ $payments->total() }}</span>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payments as $payment)
                        @php
                            $supplierName = optional(optional($payment->purchase)->supplier)->name ?? 'Unknown Supplier';
                        @endphp
                        <tr class="hover:bg-amber-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono font-bold text-gray-900">{{ $payment->payment_number }}</div>
                             </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($payment->payment_date)->format('h:i A') }}</div>
                             </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
                                            <span class="text-xs font-bold text-white">{{ strtoupper(substr($supplierName, 0, 1)) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $supplierName }}</div>
                                    </div>
                                </div>
                             </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-bold text-green-600">GHS {{ number_format($payment->amount, 2) }}</div>
                             </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $payment->payment_method == 'cash' ? 'bg-green-100 text-green-800' : 
                                       ($payment->payment_method == 'bank_transfer' ? 'bg-blue-100 text-blue-800' : 
                                       ($payment->payment_method == 'mobile_money' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                    @if($payment->payment_method == 'cash') Cash
                                    @elseif($payment->payment_method == 'bank_transfer') Bank Transfer
                                    @elseif($payment->payment_method == 'mobile_money') Mobile Money
                                    @else Cheque
                                    @endif
                                </span>
                             </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('payments.show', $payment->id) }}" class="text-amber-600 hover:text-amber-800 transition-colors" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('payments.print', $payment->id) }}" class="text-green-600 hover:text-green-800 transition-colors" title="Print Receipt" target="_blank">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                        </svg>
                                    </a>
                                </div>
                             </td>
                         </tr>
                        @empty
                         <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Payments Found</h3>
                                <p class="text-gray-500 mb-4">No supplier payments recorded for this period</p>
                            </td>
                         </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr class="font-semibold">
                            <td colspan="3" class="px-6 py-4 text-right">Total:</td>
                            <td class="px-6 py-4 text-right text-lg font-bold text-green-600">
                                GHS {{ number_format($payments->sum('amount'), 2) }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $payments->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function resetFilters() {
        window.location.href = '{{ route("reports.supplier-payments") }}';
    }
</script>
@endpush
@endsection