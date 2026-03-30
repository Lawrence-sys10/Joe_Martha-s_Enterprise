@extends('layouts.app')

@section('title', 'Customer Debt Report')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Customer Debt Report</h2>
                    <p class="text-amber-100 text-sm mt-1">Customers with outstanding balances</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('reports.daily') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Back to Reports
                    </a>
                    <a href="{{ route('reports.customer-debt.export') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @php
            $customers = \App\Models\Customer::where('current_balance', '>', 0)
                ->orWhere('current_balance', '<', 0)
                ->orderBy('current_balance', 'desc')
                ->paginate(20);
            $totalDebt = \App\Models\Customer::where('current_balance', '>', 0)->sum('current_balance');
            $totalCredit = \App\Models\Customer::where('current_balance', '<', 0)->sum('current_balance');
            $averageDebt = $customers->count() > 0 ? $totalDebt / $customers->count() : 0;
        @endphp
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Customers with Debt</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ $customers->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Outstanding Debt</p>
                        <p class="text-2xl font-bold text-red-600 mt-2">GHS {{ number_format($totalDebt, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Credit Balances</p>
                        <p class="text-2xl font-bold text-green-600 mt-2">GHS {{ number_format(abs($totalCredit), 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Average Debt</p>
                        <p class="text-2xl font-bold text-purple-600 mt-2">GHS {{ number_format($averageDebt, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Debt Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Customer Debt List</h3>
                        <p class="text-sm text-gray-500 mt-1">Customers with outstanding balances</p>
                    </div>
                    <div class="text-sm text-gray-500">
                        Total Debtors: <strong class="text-red-600">{{ $customers->total() }}</strong>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance (GHS)</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Purchases</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Payments</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit Limit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($customers as $customer)
                        @php
                            $totalPurchases = $customer->sales->sum('total');
                            $totalPayments = $customer->transactions->sum('amount');
                            $creditUsed = $customer->credit_limit ? ($customer->current_balance / $customer->credit_limit) * 100 : 0;
                            $isHighRisk = $customer->credit_limit && $customer->current_balance > $customer->credit_limit * 0.8;
                        @endphp
                        <tr class="hover:bg-amber-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                        <div class="text-xs text-gray-500">Since {{ $customer->created_at->format('M Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($customer->phone)
                                <div class="text-sm text-gray-600">📞 {{ $customer->phone }}</div>
                                @endif
                                @if($customer->email)
                                <div class="text-xs text-gray-500">✉️ {{ $customer->email }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold {{ $customer->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    GHS {{ number_format($customer->current_balance, 2) }}
                                </span>
                                @if($customer->current_balance > 0)
                                <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                    <div class="bg-red-500 h-1 rounded-full" style="width: {{ min(100, ($customer->current_balance / $totalDebt) * 100) }}%"></div>
                                </div>
                                @endif
                             </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-600">
                                GHS {{ number_format($totalPurchases, 2) }}
                             </td>
                            <td class="px-6 py-4 text-right text-sm text-green-600">
                                GHS {{ number_format($totalPayments, 2) }}
                             </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-600">
                                @if($customer->credit_limit)
                                GHS {{ number_format($customer->credit_limit, 2) }}
                                <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                    <div class="bg-amber-500 h-1 rounded-full" style="width: {{ min(100, $creditUsed) }}%"></div>
                                </div>
                                @else
                                <span class="text-gray-400">Unlimited</span>
                                @endif
                             </td>
                            <td class="px-6 py-4 text-center">
                                @if($customer->current_balance > 0)
                                    @if($isHighRisk)
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        High Risk
                                    </span>
                                    @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Owing
                                    </span>
                                    @endif
                                @elseif($customer->current_balance < 0)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Credit Balance
                                </span>
                                @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Paid Up
                                </span>
                                @endif
                             </td>
                            <td class="px-6 py-4 text-center text-sm font-medium">
                                <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-900 mr-2" title="View Details">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <button onclick="recordPayment({{ $customer->id }}, '{{ $customer->name }}', {{ $customer->current_balance }})" class="text-green-600 hover:text-green-900" title="Record Payment">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                             </td>
                         </tr>
                        @empty
                         <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-500">No customers with outstanding debt</p>
                                <p class="text-sm text-gray-400 mt-1">All customers are paid up</p>
                            </td>
                         </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr class="font-semibold">
                            <td colspan="2" class="px-6 py-4 text-right">Totals:</td>
                            <td class="px-6 py-4 text-right text-red-600">GHS {{ number_format($totalDebt, 2) }}</td>
                            <td colspan="5"></td>
                        </tr>
                    </tfoot>
                 </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $customers->withQueryString()->links() }}
            </div>
        </div>
        
        <!-- Risk Assessment Section -->
        @php
            $highRiskCustomers = $customers->filter(function($c) {
                return $c->credit_limit && $c->current_balance > $c->credit_limit * 0.8;
            });
        @endphp
        
        @if($highRiskCustomers->count() > 0)
        <div class="mt-6 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-md overflow-hidden">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-red-500 rounded-full p-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-red-800">⚠️ High Risk Customers</h3>
                            <p class="text-sm text-red-600">{{ $highRiskCustomers->count() }} customers are using >80% of their credit limit</p>
                        </div>
                    </div>
                    <a href="{{ route('customers.index') }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all">
                        Review Customers
                    </a>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Summary Note -->
        <div class="mt-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Debt is calculated from credit sales minus payments received.</span>
                <span class="ml-auto">High Risk: Customers using >80% of credit limit</span>
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
                    <p class="text-sm text-gray-600">Customer</p>
                    <p class="font-semibold text-gray-800" id="customerName"></p>
                    <p class="text-xs text-red-600 mt-1" id="balanceInfo"></p>
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
                    <input type="text" name="reference_number" placeholder="Transaction/Receipt #"
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
    let currentCustomerId = null;
    let currentBalance = null;
    
    function recordPayment(customerId, customerName, balance) {
        currentCustomerId = customerId;
        currentBalance = balance;
        
        document.getElementById('customerName').innerText = customerName;
        document.getElementById('balanceInfo').innerHTML = `Current balance: GHS ${balance.toFixed(2)}`;
        document.getElementById('paymentAmount').value = balance > 0 ? balance : 0;
        document.getElementById('paymentAmount').max = balance > 0 ? balance : 0;
        
        const form = document.getElementById('paymentForm');
        form.action = `/customers/${customerId}/payment`;
        
        document.getElementById('paymentModal').classList.remove('hidden');
        document.getElementById('paymentModal').classList.add('flex');
    }
    
    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        document.getElementById('paymentModal').classList.remove('flex');
        currentCustomerId = null;
        currentBalance = null;
    }
    
    // Validate amount doesn't exceed balance
    const amountInput = document.getElementById('paymentAmount');
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            if (currentBalance && parseFloat(this.value) > currentBalance) {
                this.value = currentBalance;
                alert('Amount cannot exceed the current balance!');
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