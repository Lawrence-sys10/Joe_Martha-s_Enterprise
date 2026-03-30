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
                @can('create customers')
                <a href="{{ route('customers.create') }}" class="bg-white hover:bg-amber-50 text-amber-600 font-bold py-2 px-4 rounded-lg shadow-md transition-all transform hover:scale-105">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Customer
                </a>
                @endcan
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Search Bar -->
        <div class="bg-white rounded-2xl shadow-lg border border-amber-100 p-6 mb-6">
            <div class="flex gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" id="search-input" placeholder="Search customers by name, email or phone..." 
                               class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                    </div>
                </div>
                <button onclick="searchCustomers()" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-2 px-6 rounded-lg transition-all">
                    Search
                </button>
            </div>
        </div>
        
        <!-- Customers Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($customers as $customer)
            <div class="bg-white rounded-2xl shadow-lg border border-amber-100 overflow-hidden hover:shadow-2xl transition-all duration-300 group">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-2xl font-bold text-white">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-500">Customer since</span>
                            <p class="text-sm font-semibold text-gray-700">{{ $customer->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-amber-600 transition-colors">{{ $customer->name }}</h3>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-gray-600">{{ $customer->email ?? 'No email' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span class="text-gray-600">{{ $customer->phone ?? 'No phone' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-gray-600">{{ Str::limit($customer->address ?? 'No address', 30) }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 border-t border-amber-100">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500">Balance</p>
                            <p class="text-lg font-bold {{ $customer->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                GHS {{ number_format($customer->current_balance, 2) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Credit Limit</p>
                            <p class="text-lg font-bold text-gray-700">GHS {{ number_format($customer->credit_limit ?? 0, 2) }}</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        @can('view customers')
                        <a href="{{ route('customers.show', $customer) }}" class="flex-1 text-center bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                            View Details
                        </a>
                        @endcan
                        @can('edit customers')
                        <a href="{{ route('customers.edit', $customer) }}" class="flex-1 text-center bg-amber-500 hover:bg-amber-600 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                            Edit
                        </a>
                        @endcan
                        @can('delete customers')
                        <button onclick="confirmDelete({{ $customer->id }})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                        <form id="delete-form-{{ $customer->id }}" action="{{ route('customers.destroy', $customer) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="bg-white rounded-2xl shadow-lg border border-amber-100 p-12 text-center">
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
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $customers->withQueryString()->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(customerId) {
        if (confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
            document.getElementById('delete-form-' + customerId).submit();
        }
    }
    
    function searchCustomers() {
        const searchTerm = document.getElementById('search-input').value;
        window.location.href = '{{ route("customers.index") }}?search=' + encodeURIComponent(searchTerm);
    }
    
    document.getElementById('search-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchCustomers();
        }
    });
</script>
@endpush
@endsection