@extends('layouts.app')

@section('title', 'Purchase Order Details')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Purchase Order Details</h2>
                    <p class="text-amber-100 text-sm mt-1">{{ $purchase->invoice_number }}</p>
                </div>
                <div class="flex gap-3">
                    @if($balance > 0)
                    <button onclick="openPaymentModal()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Make Payment
                    </button>
                    @endif
                    <a href="{{ route('suppliers.show', $purchase->supplier) }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Back to Supplier
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Purchase Information Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Purchase Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Invoice Number</p>
                        <p class="text-lg font-bold text-gray-800">{{ $purchase->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Purchase Date</p>
                        <p class="text-lg font-bold text-gray-800">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Supplier</p>
                        <p class="text-lg font-bold text-gray-800">{{ $purchase->supplier->name }}</p>
                    </div>
                    <div>
                        <!-- Empty for spacing -->
                    </div>
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">Payment Status</p>
                            <p class="text-lg font-bold {{ $purchase->payment_status == 'paid' ? 'text-green-600' : ($purchase->payment_status == 'partial' ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ ucfirst($purchase->payment_status) }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">Total Amount</p>
                            <p class="text-lg font-bold text-gray-800">GHS {{ number_format($purchase->total, 2) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">Paid Amount</p>
                            <p class="text-lg font-bold text-green-600">GHS {{ number_format($paidAmount ?? 0, 2) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-xs text-gray-500">Balance</p>
                            <p class="text-lg font-bold {{ $balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                GHS {{ number_format($balance ?? 0, 2) }}
                            </p>
                        </div>
                    </div>
                    
                    @if($balance == 0)
                    <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                        <span class="text-green-600 font-semibold">✓ Fully Paid</span>
                    </div>
                    @elseif($balance > 0 && $paidAmount > 0)
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                        <span class="text-yellow-600 font-semibold">⚠ Partially Paid - Balance: GHS {{ number_format($balance, 2) }}</span>
                    </div>
                    @elseif($balance > 0)
                    <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                        <span class="text-red-600 font-semibold">⚠ Pending Payment - Balance: GHS {{ number_format($balance, 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Items Table - Showing Cost Price (Purchase Price) -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Items Purchased</h3>
                <p class="text-sm text-gray-500 mt-1">Cost price is what you paid the supplier</p>
            </div>
            <div class="overflow-x-auto p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cost Price (GHS)</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total (GHS)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($purchase->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item->product->name }}</td>
                            <td class="px-6 py-4 text-right text-sm text-gray-600">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-right text-sm text-gray-600 font-mono">
                                @php
                                    // Use cost_price if available, otherwise use unit_price as fallback
                                    $displayCost = isset($item->cost_price) ? $item->cost_price : ($item->unit_price ?? 0);
                                @endphp
                                GHS {{ number_format($displayCost, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-semibold text-gray-800">GHS {{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-semibold">Subtotal:</td>
                            <td class="px-6 py-4 text-right font-semibold">GHS {{ number_format($purchase->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-semibold">Tax (12.5%):</td>
                            <td class="px-6 py-4 text-right font-semibold">GHS {{ number_format($purchase->tax, 2) }}</td>
                        </tr>
                        <tr class="border-t border-gray-200">
                            <td colspan="3" class="px-6 py-4 text-right font-bold">Total to Pay Supplier:</td>
                            <td class="px-6 py-4 text-right font-bold text-amber-600">GHS {{ number_format($purchase->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Payment History Section -->
        @if($purchase->payments && $purchase->payments->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden mt-6">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Payment History</h3>
            </div>
            <div class="overflow-x-auto p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($purchase->payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $payment->payment_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right text-sm font-semibold text-green-600">GHS {{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->reference_number ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
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
        
        <form id="paymentForm" method="POST" action="{{ route('purchases.payment.store', $purchase) }}">
            @csrf
            <div class="p-6 space-y-4">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-600">Purchase Order</p>
                    <p class="font-semibold text-gray-800">{{ $purchase->invoice_number }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount (GHS)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 font-semibold">₵</span>
                        </div>
                        <input type="number" name="amount" id="paymentAmount" step="0.01" required
                               class="pl-8 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                               placeholder="0.00" value="{{ $balance }}">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Remaining balance: GHS {{ number_format($balance, 2) }}</p>
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
    function openPaymentModal() {
        document.getElementById('paymentModal').classList.remove('hidden');
        document.getElementById('paymentModal').classList.add('flex');
    }
    
    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        document.getElementById('paymentModal').classList.remove('flex');
    }
    
    const amountInput = document.getElementById('paymentAmount');
    const maxAmount = {{ $balance }};
    
    if (amountInput) {
        amountInput.max = maxAmount;
        amountInput.addEventListener('input', function() {
            let value = parseFloat(this.value);
            if (value > maxAmount) {
                this.value = maxAmount;
                alert('Amount cannot exceed the remaining balance!');
            }
        });
    }
    
    document.getElementById('paymentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePaymentModal();
        }
    });
</script>
@endpush
@endsection