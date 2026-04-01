@extends('layouts.app')

@section('title', 'Record Supplier Payment')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Record Supplier Payment</h2>
                    <p class="text-amber-100 text-sm mt-1">Record payment made to supplier</p>
                </div>
                <a href="{{ route('reports.supplier-payments') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                    Back to Report
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <form method="POST" action="{{ route('reports.supplier-payments.store') }}">
                @csrf
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Supplier *</label>
                            <select name="supplier_id" required class="w-full rounded-lg border-2 border-gray-200 focus:border-amber-500 focus:ring-amber-500">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ ($selectedSupplier && $selectedSupplier->id == $supplier->id) ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('supplier_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                            <select name="payment_method" required class="w-full rounded-lg border-2 border-gray-200 focus:border-amber-500 focus:ring-amber-500">
                                <option value="cash">💵 Cash</option>
                                <option value="bank_transfer">🏦 Bank Transfer</option>
                                <option value="mobile_money">📱 Mobile Money</option>
                                <option value="cheque">📝 Cheque</option>
                            </select>
                            @error('payment_method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount (GHS) *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₵</span>
                                <input type="number" name="amount" step="0.01" required
                                       class="pl-8 w-full rounded-lg border-2 border-gray-200 focus:border-amber-500 focus:ring-amber-500"
                                       placeholder="0.00">
                            </div>
                            @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Date *</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                                   class="w-full rounded-lg border-2 border-gray-200 focus:border-amber-500 focus:ring-amber-500">
                            @error('payment_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" rows="3" class="w-full rounded-lg border-2 border-gray-200 focus:border-amber-500 focus:ring-amber-500"
                                      placeholder="Optional notes..."></textarea>
                            @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('reports.supplier-payments') }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg transition-all">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold rounded-lg shadow-md transition-all">
                            Record Payment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
