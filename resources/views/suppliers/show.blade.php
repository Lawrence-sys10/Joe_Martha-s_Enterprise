@extends('layouts.app')

@section('title', 'Supplier Details')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $supplier->name }}</h2>
                    <p class="text-amber-100 text-sm mt-1">Supplier details and purchase history</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('suppliers.purchase.create', $supplier) }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        New Purchase Order
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Back to Suppliers
                    </a>
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="bg-white hover:bg-amber-50 text-amber-600 font-bold py-2 px-4 rounded-lg shadow-md transition-all">
                        Edit Supplier
                    </a>
                </div>
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
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Supplier Info Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 p-6 text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center mx-auto shadow-lg">
                            <span class="text-3xl font-bold text-white">{{ strtoupper(substr($supplier->name, 0, 1)) }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mt-4">{{ $supplier->name }}</h3>
                        <p class="text-sm text-gray-500">Since {{ $supplier->created_at->format('M Y') }}</p>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        @if($supplier->phone)
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500">Phone</p>
                                <p class="text-sm text-gray-800">{{ $supplier->phone }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($supplier->email)
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="text-sm text-gray-800">{{ $supplier->email }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($supplier->address)
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500">Address</p>
                                <p class="text-sm text-gray-800">{{ $supplier->address }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($supplier->contact_person)
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div>
                                <p class="text-xs text-gray-500">Contact Person</p>
                                <p class="text-sm text-gray-800">{{ $supplier->contact_person }}</p>
                                @if($supplier->contact_person_phone)
                                <p class="text-xs text-gray-500">{{ $supplier->contact_person_phone }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        <div class="pt-4 border-t">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-gray-600">Balance</span>
                                <span class="text-lg font-bold {{ $supplier->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    GHS {{ number_format($supplier->current_balance, 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Payment Terms</span>
                                <span class="text-lg font-bold text-gray-700">{{ $supplier->payment_terms ?? 30 }} days</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Purchase History with View Button -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Purchase History</h3>
                                <p class="text-sm text-gray-500 mt-1">Recent purchases from this supplier</p>
                            </div>
                            <div class="text-sm text-gray-500">
                                Total Owed: <strong class="text-red-600">GHS {{ number_format($supplier->current_balance, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @php
                            $purchases = $supplier->purchases()->orderBy('created_at', 'desc')->get();
                        @endphp
                        
                        @if($purchases->count() > 0)
                        <div class="space-y-3">
                            @foreach($purchases->take(10) as $purchase)
                            @php
                                $paidAmount = $purchase->payments()->sum('amount');
                                $balance = $purchase->total - $paidAmount;
                            @endphp
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 {{ $balance > 0 ? 'bg-amber-100' : 'bg-green-100' }} rounded-full flex items-center justify-center">
                                        @if($balance > 0)
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        @else
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $purchase->invoice_number }}</p>
                                        <p class="text-xs text-gray-500">{{ $purchase->created_at->format('M d, Y') }}</p>
                                        @if($purchase->due_date)
                                        <p class="text-xs {{ $purchase->due_date < now() && $balance > 0 ? 'text-red-500' : 'text-gray-400' }}">
                                            Due: {{ $purchase->due_date->format('M d, Y') }}
                                            @if($purchase->due_date < now() && $balance > 0)
                                            <span class="text-red-500 ml-1">(Overdue)</span>
                                            @endif
                                        </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-800">GHS {{ number_format($purchase->total, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ $purchase->items->count() }} items</p>
                                    @if($balance > 0)
                                    <p class="text-xs text-red-600 mt-1">Balance: GHS {{ number_format($balance, 2) }}</p>
                                    @else
                                    <p class="text-xs text-green-600 mt-1">✓ Fully Paid</p>
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <!-- View Button -->
                                    <a href="{{ route('purchases.show', $purchase) }}" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-sm transition-colors flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </a>
                                    
                                    @if($balance > 0)
                                    <button onclick="openPaymentModal({{ $purchase->id }}, '{{ $purchase->invoice_number }}', {{ $balance }})" 
                                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-sm transition-colors flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Pay
                                    </button>
                                    @else
                                    <span class="text-green-600 text-sm flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Paid
                                    </span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        @if($purchases->count() > 10)
                        <div class="text-center mt-4">
                            <a href="{{ route('purchases.index', ['supplier_id' => $supplier->id]) }}" class="text-amber-600 hover:text-amber-800 text-sm">
                                View all {{ $purchases->count() }} purchases →
                            </a>
                        </div>
                        @endif
                        
                        @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500">No purchases recorded</p>
                            <p class="text-sm text-gray-400 mt-1">Create a purchase order to start tracking</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Record Payment</h3>
                <button onclick="closePaymentModal()" class="text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <form id="paymentForm" method="POST" action="">
            @csrf
            <div class="p-6 space-y-4">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-600">Purchase Order</p>
                    <p class="font-semibold text-gray-800" id="invoiceNumber"></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount (GHS)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 font-semibold">₵</span>
                        </div>
                        <input type="number" name="amount" id="paymentAmount" step="0.01" required
                               class="pl-8 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                               placeholder="0.00">
                    </div>
                    <p class="text-xs text-gray-500 mt-1" id="balanceInfo"></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select name="payment_method" required class="w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                           class="w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reference Number</label>
                    <input type="text" name="reference_number" placeholder="Cheque/Transaction #"
                           class="w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                              placeholder="Optional notes..."></textarea>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closePaymentModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold rounded-lg shadow-md transition-all">
                    Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentPurchaseId = null;
    let currentBalance = null;
    
    function openPaymentModal(purchaseId, invoiceNumber, balance) {
        currentPurchaseId = purchaseId;
        currentBalance = balance;
        
        document.getElementById('invoiceNumber').innerText = invoiceNumber;
        document.getElementById('balanceInfo').innerHTML = `Remaining balance: GHS ${balance.toFixed(2)}`;
        document.getElementById('paymentAmount').value = balance;
        document.getElementById('paymentAmount').max = balance;
        
        const form = document.getElementById('paymentForm');
        form.action = `/purchases/${purchaseId}/payment`;
        
        document.getElementById('paymentModal').classList.remove('hidden');
        document.getElementById('paymentModal').classList.add('flex');
    }
    
    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        document.getElementById('paymentModal').classList.remove('flex');
        currentPurchaseId = null;
        currentBalance = null;
    }
    
    // Validate amount doesn't exceed balance
    const amountInput = document.getElementById('paymentAmount');
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            if (currentBalance && parseFloat(this.value) > currentBalance) {
                this.value = currentBalance;
                alert('Amount cannot exceed the remaining balance!');
            }
        });
    }
    
    // Close modal when clicking outside
    document.getElementById('paymentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePaymentModal();
        }
    });
</script>
@endpush
@endsection
