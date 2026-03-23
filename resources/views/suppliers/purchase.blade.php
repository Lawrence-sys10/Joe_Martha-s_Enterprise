@extends('layouts.app')

@section('title', 'Create Purchase Order - ' . $supplier->name)

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Create Purchase Order</h2>
                    <p class="text-amber-100 text-sm mt-1">For: {{ $supplier->name }}</p>
                </div>
                <a href="{{ route('suppliers.show', $supplier) }}" class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white font-bold py-2 px-4 rounded-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Supplier
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('suppliers.purchase.store', $supplier) }}" id="purchaseForm">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Supplier Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden sticky top-4">
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                            <h3 class="text-lg font-semibold text-gray-800">Supplier Information</h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="bg-green-50 rounded-xl p-4 mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Selected Supplier</p>
                                        <p class="font-bold text-gray-800">{{ $supplier->name }}</p>
                                        @if($supplier->phone)
                                        <p class="text-xs text-gray-500">📞 {{ $supplier->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-t pt-4 mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Date</label>
                                <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                                
                                <label class="block text-sm font-medium text-gray-700 mb-2 mt-4">Due Date</label>
                                <input type="date" name="due_date" class="w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                                
                                <label class="block text-sm font-medium text-gray-700 mb-2 mt-4">Payment Terms</label>
                                <select name="payment_terms" class="w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                                    <option value="{{ $supplier->payment_terms }}" selected>Net {{ $supplier->payment_terms }} days (from supplier)</option>
                                    <option value="30">Net 30 days</option>
                                    <option value="15">Net 15 days</option>
                                    <option value="7">Net 7 days</option>
                                    <option value="0">Due on receipt</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Order Items</h3>
                                    <p class="text-sm text-gray-500 mt-1">Add products to this purchase order</p>
                                </div>
                                <button type="button" onclick="addItem()" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Item
                                </button>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <!-- Items Table -->
                            <div class="overflow-x-auto">
                                <table class="w-full" id="items-table">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Price (GHS)</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total (GHS)</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                                         </tr>
                                    </thead>
                                    <tbody id="items-body">
                                        <!-- Items will be added here dynamically -->
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Summary -->
                            <div class="mt-6 border-t pt-6">
                                <div class="flex justify-end">
                                    <div class="w-80 space-y-3">
                                        <div class="flex justify-between text-gray-600">
                                            <span>Subtotal:</span>
                                            <span id="subtotal">GHS 0.00</span>
                                        </div>
                                        <div class="flex justify-between text-gray-600">
                                            <span>Tax (12.5%):</span>
                                            <span id="tax">GHS 0.00</span>
                                        </div>
                                        <div class="flex justify-between text-xl font-bold pt-2 border-t">
                                            <span>Total:</span>
                                            <span id="total" class="text-amber-600">GHS 0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Notes -->
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                <textarea name="notes" rows="3" class="w-full rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200" placeholder="Any additional notes..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('suppliers.show', $supplier) }}" class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-xl transition-all">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold rounded-xl shadow-md transition-all transform hover:scale-105 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Create Purchase Order
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let items = [];
    let products = @json($products);
    let currentSupplierId = {{ $supplier->id }};
    
    function addItem() {
        const itemIndex = items.length;
        
        const productOptions = products.map(p => `<option value="${p.id}" data-price="${p.unit_price}" data-name="${p.name}">${p.name} (GHS ${p.unit_price})</option>`).join('');
        
        const row = `
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-4 py-3">
                    <select name="items[${itemIndex}][product_id]" class="product-select w-full rounded-lg border-gray-300 focus:border-amber-500" data-index="${itemIndex}" required>
                        <option value="">Select Product</option>
                        ${productOptions}
                    </select>
                 </td>
                <td class="px-4 py-3">
                    <input type="number" name="items[${itemIndex}][quantity]" class="quantity-input w-24 text-center rounded-lg border-gray-300 focus:border-amber-500" value="1" min="1" step="1" data-index="${itemIndex}" required>
                 </td>
                <td class="px-4 py-3">
                    <input type="number" name="items[${itemIndex}][unit_price]" class="price-input w-32 text-right rounded-lg border-gray-300 focus:border-amber-500" value="0" step="0.01" data-index="${itemIndex}" required>
                 </td>
                <td class="px-4 py-3 text-right">
                    <span class="item-total" data-index="${itemIndex}">GHS 0.00</span>
                 </td>
                <td class="px-4 py-3 text-center">
                    <button type="button" onclick="removeItem(${itemIndex})" class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                 </td>
             </tr>
        `;
        
        document.getElementById('items-body').insertAdjacentHTML('beforeend', row);
        items.push({ index: itemIndex });
        
        const select = document.querySelector(`select[data-index="${itemIndex}"]`);
        const quantity = document.querySelector(`input[name="items[${itemIndex}][quantity]"]`);
        const price = document.querySelector(`input[name="items[${itemIndex}][unit_price]"]`);
        
        select.addEventListener('change', function() { updateItemPrice(itemIndex); });
        quantity.addEventListener('input', function() { updateItemTotal(itemIndex); });
        price.addEventListener('input', function() { updateItemTotal(itemIndex); });
    }
    
    function updateItemPrice(index) {
        const select = document.querySelector(`select[data-index="${index}"]`);
        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption.dataset.price;
        if (price) {
            const priceInput = document.querySelector(`input[name="items[${index}][unit_price]"]`);
            priceInput.value = price;
            updateItemTotal(index);
        }
    }
    
    function updateItemTotal(index) {
        const quantity = parseInt(document.querySelector(`input[name="items[${index}][quantity]"]`).value) || 0;
        const price = parseFloat(document.querySelector(`input[name="items[${index}][unit_price]"]`).value) || 0;
        const total = quantity * price;
        document.querySelector(`.item-total[data-index="${index}"]`).innerText = `GHS ${total.toFixed(2)}`;
        updateTotals();
    }
    
    function removeItem(index) {
        const rows = document.querySelectorAll('#items-body tr');
        if (rows[index]) {
            rows[index].remove();
            items = items.filter(i => i.index !== index);
            updateTotals();
        }
    }
    
    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item-total').forEach(el => {
            const total = parseFloat(el.innerText.replace('GHS ', ''));
            subtotal += total;
        });
        const tax = subtotal * 0.125;
        const total = subtotal + tax;
        
        document.getElementById('subtotal').innerText = `GHS ${subtotal.toFixed(2)}`;
        document.getElementById('tax').innerText = `GHS ${tax.toFixed(2)}`;
        document.getElementById('total').innerText = `GHS ${total.toFixed(2)}`;
    }
    
    // Form submission validation
    document.getElementById('purchaseForm').addEventListener('submit', function(e) {
        const itemsCount = document.querySelectorAll('#items-body tr').length;
        if (itemsCount === 0) {
            e.preventDefault();
            alert('Please add at least one item to the purchase order');
            return;
        }
    });
    
    // Add first item
    addItem();
</script>
@endpush
@endsection