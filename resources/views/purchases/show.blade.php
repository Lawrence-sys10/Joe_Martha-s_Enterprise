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
                    <a href="{{ route('suppliers.show', $purchase->supplier) }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Back to Supplier
                    </a>
                    @if($purchase->balance > 0)
                    <button onclick="openPaymentModal()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-all">
                        Record Payment
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Purchase Summary -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden mb-6">
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Invoice Number</p>
                        <p class="text-lg font-bold text-gray-800">{{ $purchase->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Purchase Date</p>
                        <p class="text-lg font-bold text-gray-800">{{ $purchase->purchase_date->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Due Date</p>
                        <p class="text-lg font-bold text-gray-800">{{ $purchase->due_date ? $purchase->due_date->format('Y-m-d') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="px-2 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $purchase->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($purchase->status) }}
                        </span>
                    </div>
                </div>
                
                <!-- Payment Progress -->
                <div class="mt-6 pt-6 border-t">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Payment Status: {{ ucfirst($purchase->payment_status) }}</span>
                        <span>Paid: GHS {{ number_format($purchase->paid_amount, 2) }} / GHS {{ number_format($purchase->total, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $purchase->payment_percentage }}%"></div>
                    </div>
                    @if($purchase->balance > 0)
                    <p class="text-sm text-red-600 mt-2">Remaining Balance: GHS {{ number_format($purchase->balance, 2) }}</p>
                    @else
                    <p class="text-sm text-green-600 mt-2">✓ Fully Paid</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Items Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Items Purchased</h3>
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
                        @foreach($purchase->items as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item->product->name }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-900">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-900">GHS {{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-right font-semibold">GHS {{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-semibold">Subtotal:</td>
                            <td class="px-6 py-4 text-right">GHS {{ number_format($purchase->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right">Tax (12.5%):</td>
                            <td class="px-6 py-4 text-right">GHS {{ number_format($purchase->tax, 2) }}</td>
                        </tr>
                        <tr class="border-t-2">
                            <td colspan="3" class="px-6 py-4 text-right text-lg font-bold">Total:</td>
                            <td class="px-6 py-4 text-right text-lg font-bold text-amber-600">GHS {{ number_format($purchase->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Payment History -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Payment History</h3>
            </div>
            <div class="p-6">
                @if($purchase->payments->count() > 0)
                <div class="space-y-3">
                    @foreach($purchase->payments as $payment)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $payment->payment_number }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->payment_date->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-green-600">GHS {{ number_format($payment->amount, 2) }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500">No payments recorded yet</p>
                    @if($purchase->balance > 0)
                    <button onclick="openPaymentModal()" class="mt-2 text-amber-600 hover:text-amber-800">Record First Payment →</button>
                    @endif
                </div>
                @endif
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
        
        <form method="POST" action="{{ route('purchases.payment', $purchase) }}" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount (GHS)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 font-semibold">₵</span>
                        </div>
                        <input type="number" name="amount" step="0.01" max="{{ $purchase->balance }}" required
                               class="pl-8 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                               placeholder="0.00">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Remaining balance: GHS {{ number_format($purchase->balance, 2) }}</p>
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
            
            <div class="mt-6 flex justify-end gap-3">
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
</script>
@endpush
@endsection