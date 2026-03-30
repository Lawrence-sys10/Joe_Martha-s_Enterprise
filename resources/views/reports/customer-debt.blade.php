@extends('layouts.app')

@section('title', 'Customer Debt Report')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Customer Debt Report</h2>
                    <p class="text-amber-100 text-sm mt-1">Track customer outstanding balances</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('dashboard') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Back to Dashboard
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
            // Get customers with debt (positive balance) and credit (negative balance)
            $customersWithDebt = \App\Models\Customer::where('current_balance', '>', 0)
                ->orderBy('current_balance', 'desc')
                ->get();
            
            $customersWithCredit = \App\Models\Customer::where('current_balance', '<', 0)
                ->orderBy('current_balance', 'asc')
                ->get();
            
            $totalDebt = $customersWithDebt->sum('current_balance');
            $totalCredit = abs($customersWithCredit->sum('current_balance'));
            $netBalance = $totalDebt - $totalCredit;
        @endphp
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Customers with Debt</p>
                        <p class="text-2xl font-bold text-red-600 mt-2">{{ $customersWithDebt->count() }}</p>
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
                        <p class="text-sm text-gray-500">Total Outstanding Debt</p>
                        <p class="text-2xl font-bold text-red-600 mt-2">GHS {{ number_format($totalDebt, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Net Balance</p>
                        <p class="text-2xl font-bold {{ $netBalance >= 0 ? 'text-red-600' : 'text-green-600' }} mt-2">
                            GHS {{ number_format(abs($netBalance), 2) }} {{ $netBalance >= 0 ? '(Debt)' : '(Credit)' }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Customers with Debt Table -->
        @if($customersWithDebt->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-red-50 to-orange-50 px-6 py-4 border-b border-red-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Customers with Outstanding Debt</h3>
                        <p class="text-sm text-gray-500 mt-1">Customers who owe money</p>
                    </div>
                    <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">{{ $customersWithDebt->count() }} customers</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Outstanding Balance</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit Limit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($customersWithDebt as $customer)
                        <tr class="hover:bg-red-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-red-500 to-orange-600 flex items-center justify-center">
                                            <span class="text-sm font-bold text-white">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($customer->address ?? 'No address', 30) }}</div>
                                    </div>
                                </div>
                             </div>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->email ?? 'N/A' }}</div>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->phone ?? 'N/A' }}</div>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-red-600">GHS {{ number_format($customer->current_balance, 2) }}</span>
                                <div class="text-xs text-red-500">Due</div>
                             </div>
                            <td class="px-6 py-4 text-right text-sm text-gray-700">GHS {{ number_format($customer->credit_limit ?? 0, 2) }}</div>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                             </div>
                         </div>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr class="font-semibold">
                            <td colspan="3" class="px-6 py-4 text-right">Total Outstanding: </div>
                            <td class="px-6 py-4 text-right text-lg font-bold text-red-600">GHS {{ number_format($totalDebt, 2) }}</div>
                            <td colspan="2"></div>
                         </div>
                    </tfoot>
                 </div>
            </div>
        </div>
        @endif
        
        <!-- Customers with Credit Balance -->
        @if($customersWithCredit->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-green-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Customers with Store Credit</h3>
                        <p class="text-sm text-gray-500 mt-1">Customers who have credit balance</p>
                    </div>
                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">{{ $customersWithCredit->count() }} customers</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        32
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit Balance</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit Limit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($customersWithCredit as $customer)
                        <tr class="hover:bg-green-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                                            <span class="text-sm font-bold text-white">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($customer->address ?? 'No address', 30) }}</div>
                                    </div>
                                </div>
                             </div>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->email ?? 'N/A' }}</div>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $customer->phone ?? 'N/A' }}</div>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-green-600">GHS {{ number_format(abs($customer->current_balance), 2) }}</span>
                                <div class="text-xs text-green-500">Store Credit</div>
                             </div>
                            <td class="px-6 py-4 text-right text-sm text-gray-700">GHS {{ number_format($customer->credit_limit ?? 0, 2) }}</div>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                             </div>
                         </div>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr class="font-semibold">
                            <td colspan="3" class="px-6 py-4 text-right">Total Store Credit: </div>
                            <td class="px-6 py-4 text-right text-lg font-bold text-green-600">GHS {{ number_format($totalCredit, 2) }}</div>
                            <td colspan="2"></div>
                         </div>
                    </tfoot>
                 </div>
            </div>
        </div>
        @endif
        
        @if($customersWithDebt->count() == 0 && $customersWithCredit->count() == 0)
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-12 text-center">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-500">No customer balances found</p>
            <p class="text-sm text-gray-400 mt-1">All customers have zero balance</p>
        </div>
        @endif
        
        <!-- Summary Note -->
        <div class="mt-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Debt represents unpaid credit purchases. Store credit can be used for future purchases.</span>
            </div>
        </div>
    </div>
</div>
@endsection
