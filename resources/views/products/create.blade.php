@extends('layouts.app')

@section('title', 'Add New Product')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Add New Product</h2>
                    <p class="text-amber-100 text-sm mt-1">Create a new product for your inventory</p>
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
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" id="productForm">
                @csrf
                
                <div class="p-8 space-y-8">
                    <!-- Progress Indicator -->
                    <div class="flex justify-between mb-8">
                        <div class="flex-1 text-center">
                            <div class="w-8 h-8 bg-amber-500 text-white rounded-full flex items-center justify-center mx-auto mb-2">1</div>
                            <p class="text-xs text-gray-600">Basic Info</p>
                        </div>
                        <div class="flex-1 text-center">
                            <div class="w-8 h-8 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center mx-auto mb-2">2</div>
                            <p class="text-xs text-gray-500">Pricing & Stock</p>
                        </div>
                        <div class="flex-1 text-center">
                            <div class="w-8 h-8 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center mx-auto mb-2">3</div>
                            <p class="text-xs text-gray-500">Status</p>
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
                                    <input type="text" name="name" value="{{ old('name') }}" required
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
                                    <input type="text" name="sku" value="{{ old('sku') }}"
                                           class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Leave blank to auto-generate</p>
                                @error('sku')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
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
                                          class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">{{ old('description') }}</textarea>
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
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                    <input type="text" name="unit" value="{{ old('unit', 'piece') }}" required
                                           class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">e.g., piece, kg, liter, packet</p>
                                @error('unit')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pricing and Stock Section -->
                    <div class="section" id="section-pricing" style="display: none;">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Pricing & Stock</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price (GHS) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-400 font-semibold">₵</span>
                                    </div>
                                    <input type="number" name="unit_price" value="{{ old('unit_price') }}" step="0.01" required
                                           class="pl-8 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                </div>
                                @error('unit_price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cost Price (GHS) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-400 font-semibold">₵</span>
                                    </div>
                                    <input type="number" name="cost_price" value="{{ old('cost_price') }}" step="0.01" required
                                           class="pl-8 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                </div>
                                @error('cost_price')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    </div>
                                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" required
                                           class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                </div>
                            </div>
                            
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Stock Level</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    </div>
                                    <input type="number" name="minimum_stock" value="{{ old('minimum_stock', 0) }}"
                                           class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Alert when stock falls below this level</p>
                            </div>
                            
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tax Rate (%)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <input type="number" name="tax_rate" value="{{ old('tax_rate', 0) }}" step="0.01"
                                           class="pl-10 w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-4 bg-blue-50 rounded-xl" id="profit-preview">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-blue-700">
                                    Profit Margin: <strong class="font-bold">0%</strong>
                                    (GHS 0.00 per unit)
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Section -->
                    <div class="section" id="section-status" style="display: none;">
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
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-amber-500 focus:ring-amber-500 w-5 h-5">
                                <span class="ml-3 text-sm text-gray-700">Active (available for sale)</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 ml-8">Inactive products will not appear in POS or customer searches</p>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Buttons -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            <span class="text-red-500">*</span> Required fields
                        </div>
                        <div class="flex gap-3">
                            <button type="button" id="prevBtn" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-xl transition-all duration-200 hidden">
                                Previous
                            </button>
                            <button type="button" id="nextBtn" class="px-6 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold rounded-xl shadow-md transition-all">
                                Next
                            </button>
                            <button type="submit" id="submitBtn" class="px-6 py-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold rounded-xl shadow-md transition-all transform hover:scale-105 hidden">
                                Create Product
                            </button>
                            <a href="{{ route('products.index') }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-xl transition-all duration-200">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 3;
    
    const sections = {
        1: document.getElementById('section-basic'),
        2: document.getElementById('section-pricing'),
        3: document.getElementById('section-status')
    };
    
    const progressDots = document.querySelectorAll('.flex.justify-between .flex-1');
    
    function updateProgress() {
        // Update progress indicators
        progressDots.forEach((dot, index) => {
            const stepNumber = index + 1;
            const circle = dot.querySelector('.w-8.h-8');
            const text = dot.querySelector('.text-xs');
            
            if (stepNumber <= currentStep) {
                circle.classList.remove('bg-gray-200', 'text-gray-500');
                circle.classList.add('bg-amber-500', 'text-white');
                text.classList.remove('text-gray-500');
                text.classList.add('text-gray-600');
            } else {
                circle.classList.remove('bg-amber-500', 'text-white');
                circle.classList.add('bg-gray-200', 'text-gray-500');
                text.classList.remove('text-gray-600');
                text.classList.add('text-gray-500');
            }
        });
        
        // Show/hide sections
        for (let i = 1; i <= totalSteps; i++) {
            if (sections[i]) {
                sections[i].style.display = i === currentStep ? 'block' : 'none';
            }
        }
        
        // Update buttons
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        
        if (currentStep === 1) {
            prevBtn.classList.add('hidden');
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        } else if (currentStep === totalSteps) {
            prevBtn.classList.remove('hidden');
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            prevBtn.classList.remove('hidden');
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }
    }
    
    function validateStep(step) {
        if (step === 1) {
            const name = document.querySelector('input[name="name"]').value;
            if (!name.trim()) {
                alert('Please enter product name');
                document.querySelector('input[name="name"]').focus();
                return false;
            }
        }
        if (step === 2) {
            const unitPrice = document.querySelector('input[name="unit_price"]').value;
            const costPrice = document.querySelector('input[name="cost_price"]').value;
            
            if (!unitPrice || parseFloat(unitPrice) <= 0) {
                alert('Please enter a valid unit price');
                document.querySelector('input[name="unit_price"]').focus();
                return false;
            }
            
            if (!costPrice || parseFloat(costPrice) <= 0) {
                alert('Please enter a valid cost price');
                document.querySelector('input[name="cost_price"]').focus();
                return false;
            }
        }
        return true;
    }
    
    document.getElementById('nextBtn').addEventListener('click', function() {
        if (validateStep(currentStep) && currentStep < totalSteps) {
            currentStep++;
            updateProgress();
        }
    });
    
    document.getElementById('prevBtn').addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            updateProgress();
        }
    });
    
    // Real-time profit margin calculation
    const unitPriceInput = document.querySelector('input[name="unit_price"]');
    const costPriceInput = document.querySelector('input[name="cost_price"]');
    const profitPreview = document.getElementById('profit-preview');
    
    function updateProfitMargin() {
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        const costPrice = parseFloat(costPriceInput.value) || 0;
        const profit = unitPrice - costPrice;
        const margin = unitPrice > 0 ? (profit / unitPrice) * 100 : 0;
        
        if (profitPreview) {
            profitPreview.innerHTML = `
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
    
    // Auto-capitalize product name
    const nameInput = document.querySelector('input[name="name"]');
    if (nameInput) {
        nameInput.addEventListener('blur', function(e) {
            let value = e.target.value;
            value = value.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
            e.target.value = value;
        });
    }
    
    // Initialize
    updateProgress();
    updateProfitMargin();
</script>
@endpush
@endsection