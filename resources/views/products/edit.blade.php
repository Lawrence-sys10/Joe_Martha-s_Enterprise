@extends('layouts.app')

@section('title', 'Edit Product')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Edit Product</h2>
                    <p class="text-amber-100 text-sm mt-1">Update product information</p>
                </div>
                <a href="{{ route('products.index') }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Products
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
            <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" id="productForm">
                @csrf
                @method('PUT')
                
                <div class="p-8 space-y-8">
                    <!-- Progress Indicator -->
                    <div class="flex justify-between mb-8">
                        <div class="flex-1 text-center">
                            <div class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center mx-auto mb-2">1</div>
                            <p class="text-xs text-gray-600">Basic Info</p>
                        </div>
                        <div class="flex-1 text-center">
                            <div class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center mx-auto mb-2">2</div>
                            <p class="text-xs text-gray-600">Pricing</p>
                        </div>
                        <div class="flex-1 text-center">
                            <div class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center mx-auto mb-2">3</div>
                            <p class="text-xs text-gray-600">Status</p>
                        </div>
                    </div>
                    
                    <!-- Product Status Badge -->
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Product SKU</p>
                                    <p class="font-mono font-semibold text-gray-800">{{ $product->sku }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Created</p>
                                <p class="font-semibold text-gray-800">{{ $product->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Basic Information Section -->
                    <div class="section" id="section-basic">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Basic Information</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Product Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                           class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                </div>
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                        </svg>
                                    </div>
                                    <input type="text" value="{{ $product->sku }}" readonly
                                           class="pl-10 w-full rounded-xl border-2 border-gray-200 bg-gray-100">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">SKU cannot be changed</p>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <div class="relative">
                                <div class="absolute top-3 left-3">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                                <textarea name="description" rows="3" 
                                          class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                    </div>
                                    <select name="category_id" class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 1m3-1l3-1m-3 1v6m0 0l-3 1m3-1l3 1m-3-1v6m0 0l-3 1m3-1l3 1"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" required
                                           class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">e.g., piece, kg, liter, packet</p>
                                @error('unit')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pricing Section (Stock removed - comes from purchases) -->
                    <div class="section" id="section-pricing">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Pricing</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price (Selling Price) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-400 font-semibold">₵</span>
                                    </div>
                                    <input type="number" name="unit_price" value="{{ old('unit_price', $product->unit_price) }}" step="0.01" required
                                           class="pl-8 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">The price customers will pay</p>
                                @error('unit_price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cost Price <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-400 font-semibold">₵</span>
                                    </div>
                                    <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" required
                                           class="pl-8 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">What you pay to purchase from supplier (tax included)</p>
                                @error('cost_price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="bg-gray-50 rounded-xl p-4">
                                <label class="block text-sm font-medium text-gray-600 mb-2">Current Stock</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" value="{{ $product->stock_quantity }} {{ $product->unit }}s" readonly
                                           class="pl-10 w-full rounded-lg bg-white border-2 border-gray-200 font-bold text-lg {{ $product->stock_quantity <= $product->minimum_stock ? 'text-red-600' : 'text-green-600' }}">
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Stock is added through purchase orders</p>
                            </div>
                            
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Stock Level</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>
                                    <input type="number" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock) }}"
                                           class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Alert when stock falls below this level</p>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-4 bg-blue-50 rounded-xl">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-blue-700">
                                    Profit Margin: <strong class="font-bold">{{ number_format((($product->unit_price - $product->cost_price) / $product->unit_price) * 100, 1) }}%</strong>
                                    (GHS {{ number_format($product->unit_price - $product->cost_price, 2) }} per unit)
                                </p>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-4 bg-amber-50 rounded-xl">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-amber-700">
                                    💡 <strong>Note:</strong> Stock quantity is added through purchase orders from suppliers.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Section -->
                    <div class="section" id="section-status">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Status</h3>
                        </div>
                        
                        <div class="mt-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-amber-500 focus:ring-amber-500 w-5 h-5">
                                <span class="ml-3 text-sm text-gray-700">Active (available for sale)</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 ml-8">Inactive products will not appear in POS or customer searches</p>
                        </div>
                        
                        @if(!$product->is_active)
                        <div class="mt-4 p-4 bg-yellow-50 rounded-xl">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <p class="text-sm text-yellow-700">This product is currently inactive and will not be visible in POS</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="flex gap-4">
                            <div class="text-sm text-gray-500">
                                <span class="text-red-500">*</span> Required fields
                            </div>
                            @if($product->stockMovements->count() > 0)
                            <div class="text-sm text-amber-600">
                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $product->stockMovements->count() }} stock movement(s) recorded
                            </div>
                            @endif
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('products.index') }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-xl transition-all duration-200">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold rounded-xl shadow-md transition-all transform hover:scale-105 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Update Product
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Form validation and enhancement
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const name = document.querySelector('input[name="name"]').value;
        const unitPrice = document.querySelector('input[name="unit_price"]').value;
        const costPrice = document.querySelector('input[name="cost_price"]').value;
        
        if (!name.trim()) {
            e.preventDefault();
            alert('Please enter product name');
            document.querySelector('input[name="name"]').focus();
            return;
        }
        
        if (!unitPrice || parseFloat(unitPrice) <= 0) {
            e.preventDefault();
            alert('Please enter a valid unit price (selling price)');
            document.querySelector('input[name="unit_price"]').focus();
            return;
        }
        
        if (!costPrice || parseFloat(costPrice) <= 0) {
            e.preventDefault();
            alert('Please enter a valid cost price (purchase price)');
            document.querySelector('input[name="cost_price"]').focus();
            return;
        }
    });
    
    // Auto-capitalize product name
    const nameInput = document.querySelector('input[name="name"]');
    if (nameInput) {
        nameInput.addEventListener('blur', function(e) {
            let value = e.target.value;
            value = value.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
            e.target.value = value;
        });
    }
    
    // Calculate and display profit margin in real-time
    const unitPriceInput = document.querySelector('input[name="unit_price"]');
    const costPriceInput = document.querySelector('input[name="cost_price"]');
    const profitMarginDiv = document.querySelector('.bg-blue-50');
    
    function updateProfitMargin() {
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        const costPrice = parseFloat(costPriceInput.value) || 0;
        const profit = unitPrice - costPrice;
        const margin = unitPrice > 0 ? (profit / unitPrice) * 100 : 0;
        
        if (profitMarginDiv) {
            profitMarginDiv.innerHTML = `
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-blue-700">
                        Profit Margin: <strong class="font-bold">${margin.toFixed(1)}%</strong>
                        (GHS ${profit.toFixed(2)} per unit)
                    </p>
                </div>
            `;
        }
    }
    
    if (unitPriceInput && costPriceInput) {
        unitPriceInput.addEventListener('input', updateProfitMargin);
        costPriceInput.addEventListener('input', updateProfitMargin);
    }
    
    // Show unsaved changes warning
    let formChanged = false;
    const form = document.getElementById('productForm');
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('change', () => { formChanged = true; });
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return e.returnValue;
        }
    });
    
    form.addEventListener('submit', function() {
        formChanged = false;
    });
</script>
@endpush
@endsection