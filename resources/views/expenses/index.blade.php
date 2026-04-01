@extends('layouts.app')

@section('title', 'Expense Management')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Expense Management</h2>
                    <p class="text-amber-100 text-sm mt-1">Track and manage business expenses</p>
                </div>
                <a href="{{ route('expenses.create') }}" class="bg-white hover:bg-amber-50 text-amber-600 font-bold py-2 px-4 rounded-lg shadow-md transition-all transform hover:scale-105">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Expense
                </a>
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
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Expenses</p>
                        <p class="text-2xl font-bold text-red-600 mt-2">GHS {{ number_format($totalExpenses ?? 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Today's Expenses</p>
                        <p class="text-2xl font-bold text-orange-600 mt-2">GHS {{ number_format($todayExpenses ?? 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">This Month</p>
                        <p class="text-2xl font-bold text-purple-600 mt-2">GHS {{ number_format($monthlyExpenses ?? 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Transactions</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ $expenses->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filter-section">
            <form method="GET" class="filter-grid">
                <div>
                    <label class="filter-label">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 filter-input">
                </div>
                <div>
                    <label class="filter-label">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 filter-input">
                </div>
                <div>
                    <label class="filter-label">Category</label>
                    <select name="category" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 filter-select">
                        <option value="">All Categories</option>
                        <option value="Rent" {{ request('category') == 'Rent' ? 'selected' : '' }}>🏢 Rent</option>
                        <option value="Utilities" {{ request('category') == 'Utilities' ? 'selected' : '' }}>💡 Utilities</option>
                        <option value="Salaries" {{ request('category') == 'Salaries' ? 'selected' : '' }}>👥 Salaries</option>
                        <option value="Transport" {{ request('category') == 'Transport' ? 'selected' : '' }}>🚗 Transport</option>
                        <option value="Maintenance" {{ request('category') == 'Maintenance' ? 'selected' : '' }}>🔧 Maintenance</option>
                        <option value="Marketing" {{ request('category') == 'Marketing' ? 'selected' : '' }}>📢 Marketing</option>
                        <option value="Office Supplies" {{ request('category') == 'Office Supplies' ? 'selected' : '' }}>📎 Office Supplies</option>
                        <option value="Taxes" {{ request('category') == 'Taxes' ? 'selected' : '' }}>📑 Taxes</option>
                        <option value="Insurance" {{ request('category') == 'Insurance' ? 'selected' : '' }}>🛡️ Insurance</option>
                        <option value="Other" {{ request('category') == 'Other' ? 'selected' : '' }}>📝 Other</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="w-full filter-btn text-white font-bold py-2 px-4 rounded-lg transition-all">Apply</button>
                    <a href="{{ route('expenses.index') }}" class="clear-btn text-white font-bold py-2 px-4 rounded-lg transition-all">Reset</a>
                </div>
            </form>
        </div>
        
        <!-- Expenses Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Expense Transactions</h3>
                        <p class="text-sm text-gray-500 mt-1">All recorded expenses</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Total: {{ $expenses->total() }}</span>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($expenses as $expense)
                        <tr class="hover:bg-amber-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm font-mono font-bold text-gray-900">{{ $expense->invoice_number }}</td>
                            <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $expense->category }}</span></td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($expense->description, 50) }}</td>
                            <td class="px-6 py-4 text-right"><span class="text-sm font-bold text-red-600">GHS {{ number_format($expense->amount, 2) }}</span></td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('expenses.show', $expense) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('expenses.edit', $expense) }}" class="text-amber-600 hover:text-amber-800" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button onclick="confirmDelete({{ $expense->id }})" class="text-red-600 hover:text-red-800" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                    <form id="delete-form-{{ $expense->id }}" action="{{ route('expenses.destroy', $expense) }}" method="POST" style="display: none;">@csrf @method('DELETE')</form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-gray-500">No expenses recorded yet</p>
                                <p class="text-sm text-gray-400 mt-1">Click "Add Expense" to record your first expense</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr class="font-semibold">
                            <td colspan="4" class="px-6 py-4 text-right">Total: </td>
                            <td class="px-6 py-4 text-right text-lg font-bold text-red-600">GHS {{ number_format($expenses->sum('amount'), 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $expenses->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this expense? This action cannot be undone.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endsection

