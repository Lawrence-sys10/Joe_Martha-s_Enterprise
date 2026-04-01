@extends('layouts.app')

@section('title', 'Cash Flow Statement')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Cash Flow Statement</h2>
                    <p class="text-amber-100 text-sm mt-1">Track your cash movements</p>
                </div>
                <a href="{{ route('dashboard') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Date Range Filter -->
        <div class="filter-section">
            <form method="GET" class="filter-grid">
                <div>
                    <label class="filter-label">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 filter-input">
                </div>
                <div>
                    <label class="filter-label">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 filter-input">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="filter-btn text-white font-bold py-2 px-6 rounded-lg transition-all">
                        Generate Report
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Cash Flow Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Opening Balance</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">GHS {{ number_format($cashFlow['summary']['opening_balance'] ?? 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Net Cash Flow</p>
                        <p class="text-2xl font-bold {{ ($cashFlow['operating_activities']['net_cash'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                            GHS {{ number_format($cashFlow['operating_activities']['net_cash'] ?? 0, 2) }}
                        </p>
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
                        <p class="text-sm text-gray-500">Closing Balance</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">GHS {{ number_format($cashFlow['summary']['closing_balance'] ?? 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cash Flow Details -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <h3 class="text-lg font-semibold text-gray-800">Operating Activities</h3>
                <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <span class="text-gray-600">Cash Sales</span>
                        <span class="text-lg font-semibold text-gray-800">GHS {{ number_format($cashFlow['operating_activities']['cash_sales'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <span class="text-gray-600">Customer Payments Received</span>
                        <span class="text-lg font-semibold text-gray-800">GHS {{ number_format($cashFlow['operating_activities']['customer_receipts'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200 bg-green-50">
                        <span class="text-gray-700 font-semibold">Total Cash Inflow</span>
                        <span class="text-lg font-bold text-green-600">GHS {{ number_format($cashFlow['operating_activities']['total_inflow'] ?? 0, 2) }}</span>
                    </div>
                    
                    <div class="mt-4 pt-2">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600">Purchases (Payments to Suppliers)</span>
                            <span class="text-lg font-semibold text-gray-800">GHS {{ number_format($cashFlow['operating_activities']['purchases'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600">Supplier Payments</span>
                            <span class="text-lg font-semibold text-gray-800">GHS {{ number_format($cashFlow['operating_activities']['supplier_payments'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600">Operating Expenses</span>
                            <span class="text-lg font-semibold text-gray-800">GHS {{ number_format($cashFlow['operating_activities']['expenses'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 bg-red-50">
                            <span class="text-gray-700 font-semibold">Total Cash Outflow</span>
                            <span class="text-lg font-bold text-red-600">GHS {{ number_format($cashFlow['operating_activities']['total_outflow'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-4 border-t-2 border-gray-300">
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-gray-800">Net Cash from Operations</span>
                            <span class="text-2xl font-bold {{ ($cashFlow['operating_activities']['net_cash'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                GHS {{ number_format($cashFlow['operating_activities']['net_cash'] ?? 0, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Summary Note -->
        <div class="mt-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Cash flow from operating activities represents the cash generated from business operations.</span>
            </div>
        </div>
    </div>
</div>
@endsection

