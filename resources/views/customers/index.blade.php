@extends('layouts.app')

@section('title', 'Customer Management')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Customer Management</h2>
                    <p class="text-amber-100 text-sm mt-1">Manage your valued customers and their purchases</p>
                </div>
                <div class="flex gap-3">
                    @can('create customers')
                    <a href="{{ route('customers.create') }}" class="bg-white hover:bg-amber-50 text-amber-600 font-bold py-2 px-4 rounded-lg shadow-md transition-all transform hover:scale-105 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Customer
                    </a>
                    @endcan
                    <a href="{{ route('customers.index') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all">
                        Refresh
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
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Customers</p>
                        <p class="text-2xl font-bold text-gray-800 mt-2">{{ $customers->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    <span class="text-green-600">{{ $customers->where('is_active', true)->count() }}</span> active
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Balance Due</p>
                        <p class="text-2xl font-bold text-red-600 mt-2">GHS {{ number_format($customers->sum('current_balance'), 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    {{ $customers->where('current_balance', '>', 0)->count() }} customers owe
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Average Credit Limit</p>
                        <p class="text-2xl font-bold text-purple-600 mt-2">GHS {{ number_format($customers->avg('credit_limit') ?? 0, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    Total: GHS {{ number_format($customers->sum('credit_limit'), 2) }}
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-xl border border-amber-100 p-6 hover:shadow-xl transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Store Credit</p>
                        <p class="text-2xl font-bold text-green-600 mt-2">GHS {{ number_format(abs($customers->where('current_balance', '<', 0)->sum('current_balance')), 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    {{ $customers->where('current_balance', '<', 0)->count() }} customers have credit
                </div>
            </div>
        </div>
        
        <!-- Search Bar -->
        <div class="bg-white rounded-2xl shadow-lg border border-amber-100 p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" id="search-input" placeholder="      Search customers by name, email or phone..." value="{{ request('search') }}"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                    </div>
                </div>
                <button onclick="searchCustomers()" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-2 px-6 rounded-lg transition-all">
                    Search
                </button>
                @if(request('search'))
                <a href="{{ route('customers.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg transition-all text-center">
                    Clear
                </a>
                @endif
            </div>
        </div>
        
        <!-- Customers Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Customer List</h3>
                        <p class="text-sm text-gray-500 mt-1">All registered customers</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Active: {{ $customers->where('is_active', true)->count() }}</span>
                        <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">With Balance: {{ $customers->where('current_balance', '>', 0)->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Credit Limit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Since</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($customers as $customer)
                        <tr class="hover:bg-amber-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono text-gray-500">#{{ $customer->id }}</span>
                             </div>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
                                            <span class="text-sm font-bold text-white">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($customer->address ?? 'No address', 30) }}</div>
                                    </div>
                                </div>
                             </div>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($customer->email)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>{{ Str::limit($customer->email, 25) }}</span>
                                    </div>
                                    @endif
                                    @if($customer->phone)
                                    <div class="flex items-center gap-1 mt-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        <span>{{ $customer->phone }}</span>
                                    </div>
                                    @endif
                                </div>
                             </div>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-bold {{ $customer->current_balance > 0 ? 'text-red-600' : ($customer->current_balance < 0 ? 'text-green-600' : 'text-gray-600') }}">
                                    GHS {{ number_format($customer->current_balance, 2) }}
                                </span>
                                @if($customer->current_balance > 0)
                                <div class="text-xs text-red-500">Due</div>
                                @endif
                             </div>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm text-gray-700">GHS {{ number_format($customer->credit_limit ?? 0, 2) }}</span>
                             </div>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm text-gray-500">{{ $customer->created_at->format('M d, Y') }}</span>
                             </div>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="showAddCreditModal({{ $customer->id }}, '{{ addslashes($customer->name) }}')" 
                                            class="text-green-600 hover:text-green-800 transition-colors p-1" 
                                            title="Add Credit Item">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                    
                                    @can('view customers')
                                    <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800 transition-colors p-1" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @endcan
                                    
                                    @can('edit customers')
                                    <a href="{{ route('customers.edit', $customer) }}" class="text-amber-600 hover:text-amber-800 transition-colors p-1" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @endcan
                                </div>
                             </div>
                         </div>
                        @empty
                        32
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Customers Found</h3>
                                <p class="text-gray-500 mb-4">Get started by adding your first customer</p>
                                @can('create customers')
                                <a href="{{ route('customers.create') }}" class="inline-flex items-center bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Customer
                                </a>
                                @endcan
                             </div>
                         </div>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr class="font-semibold">
                            <td colspan="3" class="px-6 py-4 text-right">Total Balance Due: </div>
                            <td class="px-6 py-4 text-right text-lg font-bold text-red-600">
                                GHS {{ number_format($customers->sum('current_balance'), 2) }}
                             </div>
                            <td colspan="3"></div>
                         </div>
                    </tfoot>
                 </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $customers->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Credit Item Modal -->
<div id="addCreditModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-4 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Add Credit Item</h3>
                <button onclick="closeAddCreditModal()" class="text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <form id="addCreditForm" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="customer_id" id="credit_customer_id">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                <input type="text" id="credit_customer_name" readonly class="w-full rounded-lg border-gray-300 bg-gray-50">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Product *</label>
                <select name="product_id" id="credit_product_id" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500" required>
                    <option value="">Select Product</option>
                    @foreach(\App\Models\Product::where('is_active', true)->get() as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}">{{ $product->name }} - GHS {{ number_format($product->unit_price, 2) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input type="number" name="quantity" id="credit_quantity" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500" value="1" min="1" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price (GHS)</label>
                    <input type="number" name="unit_price" id="credit_unit_price" class="w-full rounded-lg border-gray-300 bg-gray-50" readonly step="0.01">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount (GHS)</label>
                <input type="number" name="total" id="credit_total" class="w-full rounded-lg border-gray-300 bg-gray-50" readonly step="0.01">
            </div>
            
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeAddCreditModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors shadow-md">Add Credit Item</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentCustomerId = null;
    
    function showAddCreditModal(customerId, customerName) {
        currentCustomerId = customerId;
        document.getElementById('credit_customer_id').value = customerId;
        document.getElementById('credit_customer_name').value = customerName;
        document.getElementById('addCreditForm').action = '/customers/' + customerId + '/add-credit';
        document.getElementById('addCreditModal').classList.remove('hidden');
        document.getElementById('addCreditModal').classList.add('flex');
        
        // Reset form
        document.getElementById('credit_product_id').value = '';
        document.getElementById('credit_quantity').value = '1';
        document.getElementById('credit_unit_price').value = '';
        document.getElementById('credit_total').value = '';
    }
    
    function closeAddCreditModal() {
        document.getElementById('addCreditModal').classList.add('hidden');
        document.getElementById('addCreditModal').classList.remove('flex');
        currentCustomerId = null;
    }
    
    // Calculate total when product or quantity changes
    document.getElementById('credit_product_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        if (price && this.value) {
            document.getElementById('credit_unit_price').value = parseFloat(price).toFixed(2);
            calculateTotal();
        } else {
            document.getElementById('credit_unit_price').value = '';
            document.getElementById('credit_total').value = '';
        }
    });
    
    document.getElementById('credit_quantity').addEventListener('input', function() {
        calculateTotal();
    });
    
    function calculateTotal() {
        const quantity = parseFloat(document.getElementById('credit_quantity').value) || 0;
        const price = parseFloat(document.getElementById('credit_unit_price').value) || 0;
        const total = quantity * price;
        document.getElementById('credit_total').value = total.toFixed(2);
    }
    
    function confirmDelete(customerId) {
        if (confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
            document.getElementById('delete-form-' + customerId).submit();
        }
    }
    
    function searchCustomers() {
        const searchTerm = document.getElementById('search-input').value;
        if (searchTerm.trim()) {
            window.location.href = '{{ route("customers.index") }}?search=' + encodeURIComponent(searchTerm);
        } else {
            window.location.href = '{{ route("customers.index") }}';
        }
    }
    
    // Allow Enter key to trigger search
    document.getElementById('search-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchCustomers();
        }
    });
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('addCreditModal');
        if (event.target === modal) {
            closeAddCreditModal();
        }
    }
</script>
@endpush
@endsection
