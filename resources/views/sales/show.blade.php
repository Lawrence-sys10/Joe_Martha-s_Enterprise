@extends('layouts.app')

@section('title', 'Sale Details')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Sale Details</h2>
                    <p class="text-amber-100 text-sm mt-1">Invoice: {{ $sale->invoice_number }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('sales.index') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Back to Sales
                    </a>
                    @if($sale->payment_status == 'paid')
                    <a href="{{ route('sales.receipt', $sale) }}" target="_blank" class="bg-white hover:bg-amber-50 text-amber-600 font-bold py-2 px-4 rounded-lg shadow-md transition-all">
                        Print Receipt
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Sale Summary Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Sale Summary</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Invoice Number</p>
                        <p class="text-lg font-bold text-gray-800">{{ $sale->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date & Time</p>
                        <p class="text-lg font-bold text-gray-800">{{ $sale->sale_date->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Cashier</p>
                        <p class="text-lg font-bold text-gray-800">{{ $sale->user->name ?? 'System' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="px-2 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $sale->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($sale->status) }}
                        </span>
                    </div>
                </div>
                
                @if($sale->customer)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">Customer</p>
                    <p class="text-md font-semibold text-gray-800">{{ $sale->customer->name }}</p>
                    <p class="text-sm text-gray-600">{{ $sale->customer->phone ?? 'No phone' }} | {{ $sale->customer->email ?? 'No email' }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Items Table - Tax Removed -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Items Sold</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sale->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item->product->name }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-900">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-900">GHS {{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-right font-semibold text-gray-900">GHS {{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-semibold text-gray-800">Subtotal:</td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-800">GHS {{ number_format($sale->subtotal, 2) }}</td>
                        </tr>
                        @if($sale->discount > 0)
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-green-700">Discount:</td>
                            <td class="px-6 py-4 text-right text-green-700">-GHS {{ number_format($sale->discount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-t-2 border-gray-300">
                            <td colspan="3" class="px-6 py-4 text-right text-lg font-bold text-amber-700">Total:</td>
                            <td class="px-6 py-4 text-right text-lg font-bold text-amber-700">GHS {{ number_format($sale->total, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-gray-700">Amount Paid:</td>
                            <td class="px-6 py-4 text-right text-gray-700">GHS {{ number_format($sale->paid_amount, 2) }}</td>
                        </tr>
                        @if($sale->payment_method != 'credit')
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-gray-700">Change:</td>
                            <td class="px-6 py-4 text-right text-green-700 font-semibold">GHS {{ number_format($sale->change_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($sale->payment_method == 'credit' && $sale->payment_status != 'paid')
                        <tr class="bg-yellow-50">
                            <td colspan="3" class="px-6 py-4 text-right text-yellow-700 font-semibold">Remaining Balance:</td>
                            <td class="px-6 py-4 text-right text-yellow-700 font-bold">GHS {{ number_format($sale->total - $sale->paid_amount, 2) }}</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Payment Information -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Payment Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Payment Method</p>
                        <p class="text-md font-semibold text-gray-800">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Payment Status</p>
                        <span class="px-2 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            {{ $sale->payment_status == 'paid' ? 'bg-green-100 text-green-800' : 
                               ($sale->payment_status == 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($sale->payment_status) }}
                        </span>
                    </div>
                </div>
                
                @if($sale->payments->count() > 0)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-2">Payment Transactions</p>
                    @foreach($sale->payments as $payment)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg mb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        <p class="text-lg font-bold text-green-600">GHS {{ number_format($payment->amount, 2) }}</p>
                    </div>
                    @endforeach
                </div>
                @endif
                
                @if($sale->payment_status != 'paid' && $sale->payment_method == 'credit')
                <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm font-semibold text-yellow-800">Payment Required</p>
                    </div>
                    <p class="text-sm text-yellow-700">Remaining balance: GHS {{ number_format($sale->total - $sale->paid_amount, 2) }}</p>
                    <p class="text-xs text-yellow-600 mt-1">Receipt will be available once full payment is received.</p>
                </div>
                @endif
                
                @if($sale->notes)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">Notes</p>
                    <p class="text-sm text-gray-700 mt-1">{{ $sale->notes }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Actions -->
        @if($sale->payment_status != 'paid')
        <div class="mt-6">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                <svg class="w-8 h-8 text-yellow-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-yellow-800 font-medium">Payment Pending</p>
                <p class="text-sm text-yellow-600 mt-1">Please complete payment to print receipt.</p>
                <p class="text-xs text-yellow-500 mt-2">Remaining: GHS {{ number_format($sale->total - $sale->paid_amount, 2) }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection