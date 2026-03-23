@extends('layouts.app')

@section('title', 'Payment Details')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Payment Details</h2>
                    <p class="text-amber-100 text-sm mt-1">{{ $payment->payment_number }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('payments.index') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Back to Payments
                    </a>
                    <a href="{{ route('payments.receipt', $payment) }}" target="_blank" class="bg-white hover:bg-amber-50 text-amber-600 font-bold py-2 px-4 rounded-lg shadow-md transition-all">
                        Print Receipt
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Payment Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Payment Number</p>
                                <p class="text-lg font-mono font-bold text-gray-800">{{ $payment->payment_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Date & Time</p>
                                <p class="text-lg text-gray-800">{{ $payment->payment_date->format('Y-m-d H:i:s') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Amount</p>
                                <p class="text-2xl font-bold text-green-600">GHS {{ number_format($payment->amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Payment Method</p>
                                <p class="text-lg text-gray-800">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                            </div>
                            @if($payment->reference_number)
                            <div>
                                <p class="text-sm text-gray-500">Reference Number</p>
                                <p class="text-lg text-gray-800">{{ $payment->reference_number }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Purchase Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Purchase Information</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Purchase Order</p>
                                <a href="{{ route('purchases.show', $payment->purchase) }}" class="text-lg font-mono font-bold text-amber-600 hover:text-amber-800">
                                    {{ $payment->purchase->invoice_number }}
                                </a>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Supplier</p>
                                <p class="text-lg font-bold text-gray-800">{{ $payment->purchase->supplier->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Purchase Date</p>
                                <p class="text-lg text-gray-800">{{ $payment->purchase->purchase_date->format('Y-m-d') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Amount</p>
                                <p class="text-lg text-gray-800">GHS {{ number_format($payment->purchase->total, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($payment->notes)
                <div class="mt-6 pt-6 border-t">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Notes</h3>
                    <p class="text-gray-600">{{ $payment->notes }}</p>
                </div>
                @endif
                
                <div class="mt-6 pt-6 border-t">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Processed by: {{ $payment->user->name ?? 'System' }}</span>
                        <span>Receipt generated: {{ $payment->created_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection