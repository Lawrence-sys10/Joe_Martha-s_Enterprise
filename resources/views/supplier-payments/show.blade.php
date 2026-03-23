@extends('layouts.app')

@section('title', 'Payment Details')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Payment Details</h2>
                    <p class="text-amber-100 text-sm mt-1">{{ $supplierPayment->payment_number }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('suppliers.show', $supplierPayment->supplier) }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Back to Supplier
                    </a>
                    <a href="{{ route('supplier-payments.receipt', $supplierPayment) }}" target="_blank" class="bg-white hover:bg-amber-50 text-amber-600 font-bold py-2 px-4 rounded-lg shadow-md transition-all">
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
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Payment Number</p>
                                <p class="text-lg font-mono font-bold text-gray-800">{{ $supplierPayment->payment_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Date & Time</p>
                                <p class="text-lg text-gray-800">{{ $supplierPayment->payment_date->format('Y-m-d H:i:s') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Amount</p>
                                <p class="text-2xl font-bold text-green-600">GHS {{ number_format($supplierPayment->amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Payment Method</p>
                                <p class="text-lg text-gray-800">{{ ucfirst(str_replace('_', ' ', $supplierPayment->payment_method)) }}</p>
                            </div>
                            @if($supplierPayment->reference_number)
                            <div>
                                <p class="text-sm text-gray-500">Reference Number</p>
                                <p class="text-lg text-gray-800">{{ $supplierPayment->reference_number }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Supplier Information</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Supplier Name</p>
                                <p class="text-lg font-bold text-gray-800">{{ $supplierPayment->supplier->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="text-gray-800">{{ $supplierPayment->supplier->email ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="text-gray-800">{{ $supplierPayment->supplier->phone ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($supplierPayment->notes)
                <div class="mt-6 pt-6 border-t">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Notes</h3>
                    <p class="text-gray-600">{{ $supplierPayment->notes }}</p>
                </div>
                @endif
                
                <div class="mt-6 pt-6 border-t">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Processed by: {{ $supplierPayment->user->name ?? 'System' }}</span>
                        <span>Receipt generated: {{ $supplierPayment->created_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection