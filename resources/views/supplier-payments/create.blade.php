@extends('layouts.app')

@section('title', 'Record Supplier Payment')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Record Payment</h2>
                    <p class="text-amber-100 text-sm mt-1">Record payment to {{ $supplier->name }}</p>
                </div>
                <a href="{{ route('suppliers.show', $supplier) }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                    Back to Supplier
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <form method="POST" action="{{ route('supplier-payments.store') }}">
                @csrf
                <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                
                <div class="p-8 space-y-6">
                    <!-- Supplier Info -->
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-4 mb-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Supplier</p>
                                <p class="text-lg font-bold text-gray-800">{{ $supplier->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Current Balance</p>
                                <p class="text-xl font-bold {{ $supplier->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    GHS {{ number_format($supplier->current_balance, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Details -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Amount (GHS) *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-400 font-semibold">₵</span>
                                    </div>
                                    <input type="number" name="amount" step="0.01" required
                                           class="pl-8 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200"
                                           placeholder="0.00">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                                <select name="payment_method" required class="w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Date *</label>
                                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                                       class="w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                                <input type="text" name="reference_number" placeholder="Cheque/Transaction #"
                                       class="w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" rows="3" 
                                      class="w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                                      placeholder="Optional payment notes..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('suppliers.show', $supplier) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded-lg transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-all">
                            Record Payment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection