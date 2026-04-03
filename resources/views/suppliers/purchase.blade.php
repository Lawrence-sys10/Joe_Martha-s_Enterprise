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
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('suppliers.purchase.store', $supplier) }}" id="purchaseForm">
            @csrf
            
            <!-- Order Details Card -->
            <div class="bg-white rounded-xl shadow-md border border-amber-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-3 border-b border-amber-100">
                    <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Supplier Information
                    </h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                        <div class="md:col-span-1">
                            <div class="bg-amber-50 rounded-lg p-3 border border-amber-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ strtoupper(substr($supplier->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-amber-600 font-medium">Supplier</p>
                                        <p class="font-medium text-gray-800 text-sm">{{ $supplier->name }}</p>
                                        @if($supplier->phone)
                                        <p class="text-xs text-gray-500">{{ $supplier->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Purchase Date</label>
                            <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required 
                                   class="w-full rounded-lg border-gray-200 focus:border-amber-500 focus:ring-amber-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label>
                            <input type="date" name="due_date" 
                                   class="w-full rounded-lg border-gray-200 focus:border-amber-500 focus:ring-amber-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Payment Terms</label>
                            <select name="payment_terms" class="w-full rounded-lg border-gray-200 focus:border-amber-500 focus:ring-amber-500 text-sm">
                                <option value="{{ $supplier->payment_terms }}" selected>Net {{ $supplier->payment_terms }} days</option>
                                <option value="30">Net 30 days</option>
                                <option value="15">Net 15 days</option>
                                <option value="7">Net 7 days</option>
                                <option value="0">Due on receipt</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Items Section -->
            <div class="bg-white rounded-xl shadow-md border border-amber-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-3 border-b border-amber-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Order Items
                            </h3>
                            <p class="text-xs text-gray-500">Add products to this purchase order</p>
                        </div>
                        <button type="button" onclick="addItem()" class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Item
                        </button>
                    </div>
                </div>
                
                <div class="p-5">
                    <!-- Items Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-20">Qty</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-28">Current Cost</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-28">New Cost (Purchase Price)</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-28">Current Selling Price</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-28">New Selling Price</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-28">Total (Cost × Qty)</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-12"></th>
                                 </thead>
                            <tbody id="items-body" class="bg-white divide-y divide-gray-200">
                                <!-- Items will be added here dynamically -->
                            </tbody>
                         </div>
                    </div>
                    
                    <!-- Empty State -->
                    <div id="empty-state" class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <p class="text-gray-400 text-sm">No items added yet</p>
                        <p class="text-gray-400 text-xs mt-1">Click "Add Item" to start adding products</p>
                    </div>
                    
                    <!-- Summary - NO TAX (supplier includes tax in cost price) -->
                    <div class="mt-6 border-t border-gray-200 pt-4 hidden" id="summary-section">
                        <div class="flex justify-end">
                            <div class="w-80">
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Total Purchase Cost:</span>
                                        <span id="subtotal" class="font-semibold text-gray-800">GHS 0.00</span>
                                    </div>
                                    <div class="border-t border-gray-200 pt-2 mt-1">
                                        <div class="flex justify-between">
                                            <span class="font-bold text-gray-800">Total to Pay Supplier:</span>
                                            <span id="total" class="font-bold text-amber-600 text-lg">GHS 0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Notes Section -->
            <div class="bg-white rounded-xl shadow-md border border-amber-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-3 border-b border-amber-100">
                    <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Additional Notes
                    </h3>
                </div>
                <div class="p-5">
                    <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-200 focus:border-amber-500 focus:ring-amber-500 text-sm" placeholder="Any additional notes or instructions..."></textarea>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('suppliers.show', $supplier) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-all text-sm">
                    Cancel
                </a>
                <button type="submit" id="submit-btn" class="px-5 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-medium rounded-lg shadow-sm transition-all text-sm" disabled>
                    Create Purchase Order
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 34px;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }
    
    .select2-container--focus .select2-selection--single {
        border-color: #f59e0b;
        box-shadow: 0 0 0 2px #fef3c7;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px;
        padding-left: 10px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px;
        right: 6px;
    }
    
    .select2-dropdown {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }
    
    .select2-search__field {
        border: 1px solid #e5e7eb !important;
        border-radius: 0.375rem !important;
        padding: 6px !important;
        font-size: 0.875rem !important;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #f59e0b;
    }
    
    .new-item-row {
        animation: fadeIn 0.2s ease;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .quantity-input, .cost-input, .unit-price-input {
        font-size: 0.875rem;
        padding: 0.375rem 0.5rem;
    }
    
    .quantity-input {
        text-align: center;
    }
    
    .cost-input, .unit-price-input {
        text-align: right;
    }
    
    .current-price {
        background-color: #f3f4f6;
        color: #6b7280;
    }
    
    .price-increase {
        color: #10b981;
        font-weight: 500;
    }
    
    .price-decrease {
        color: #ef4444;
        font-weight: 500;
    }
    
    .price-same {
        color: #6b7280;
    }
    
    .recommendation-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 500;
        margin-top: 6px;
    }
    
    .recommendation-up {
        background-color: #d1fae5;
        color: #065f46;
        border-left: 3px solid #10b981;
    }
    
    .recommendation-down {
        background-color: #fee2e2;
        color: #991b1b;
        border-left: 3px solid #ef4444;
    }
    
    .recommendation-hold {
        background-color: #fef3c7;
        color: #92400e;
        border-left: 3px solid #f59e0b;
    }
    
    .alert-pulse {
        animation: pulse 1s ease-in-out;
    }
    
    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(1.02); }
        100% { opacity: 1; transform: scale(1); }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let itemCount = 0;
    let products = @json($products);
    const TARGET_MARGIN = 30;
    
    function updateSubmitButton() {
        const itemsCount = document.querySelectorAll('#items-body tr').length;
        const submitBtn = document.getElementById('submit-btn');
        const emptyState = document.getElementById('empty-state');
        const summarySection = document.getElementById('summary-section');
        
        if (itemsCount > 0) {
            submitBtn.disabled = false;
            if (emptyState) emptyState.classList.add('hidden');
            if (summarySection) summarySection.classList.remove('hidden');
        } else {
            submitBtn.disabled = true;
            if (emptyState) emptyState.classList.remove('hidden');
            if (summarySection) summarySection.classList.add('hidden');
        }
    }
    
    function calculateRecommendedPrice(costPrice) {
        return Math.round((costPrice * (1 + TARGET_MARGIN / 100)) * 100) / 100;
    }
    
    function getRecommendationMessage(productName, currentCost, newCost, currentSellPrice, newSellPrice) {
        const costChange = newCost - currentCost;
        const costChangePercent = currentCost > 0 ? (costChange / currentCost) * 100 : 0;
        const currentMargin = currentCost > 0 ? ((currentSellPrice - currentCost) / currentCost) * 100 : 0;
        const newMargin = newCost > 0 ? ((newSellPrice - newCost) / newCost) * 100 : 0;
        const recommendedPrice = calculateRecommendedPrice(newCost);
        
        let message = '';
        let type = '';
        
        if (costChange > 0) {
            if (newMargin < 20) {
                type = 'up';
                message = `⚠️ Cost increased by ${costChangePercent.toFixed(1)}%. Margin dropped to ${newMargin.toFixed(1)}%. Recommended: GHS ${recommendedPrice.toFixed(2)} (${TARGET_MARGIN}% margin). Increase price to maintain profit.`;
            } else if (newMargin > 50) {
                type = 'down';
                message = `✅ Cost increased by ${costChangePercent.toFixed(1)}% but margin is healthy at ${newMargin.toFixed(1)}%. You have room to reduce price.`;
            } else {
                type = 'hold';
                message = `📊 Cost increased by ${costChangePercent.toFixed(1)}%. Margin: ${newMargin.toFixed(1)}% (target: ${TARGET_MARGIN}%). Current price is acceptable.`;
            }
        } else if (costChange < 0) {
            if (newMargin > 45) {
                type = 'down';
                message = `🎉 Cost decreased by ${Math.abs(costChangePercent).toFixed(1)}%! Margin is ${newMargin.toFixed(1)}%. You can reduce price to GHS ${recommendedPrice.toFixed(2)} to stay competitive.`;
            } else {
                type = 'hold';
                message = `📉 Cost decreased by ${Math.abs(costChangePercent).toFixed(1)}%. New margin: ${newMargin.toFixed(1)}%. Consider keeping current price for better profit.`;
            }
        } else {
            if (newMargin < 20) {
                type = 'up';
                message = `⚠️ Your profit margin is low (${newMargin.toFixed(1)}%). Consider increasing to GHS ${recommendedPrice.toFixed(2)}.`;
            } else if (newMargin > 50) {
                type = 'down';
                message = `✅ Your margin is excellent at ${newMargin.toFixed(1)}%. You can reduce price for competitive advantage.`;
            } else {
                type = 'hold';
                message = `👍 Good pricing! Margin: ${newMargin.toFixed(1)}% (target: ${TARGET_MARGIN}%). No change needed.`;
            }
        }
        
        return { message, type, recommendedPrice };
    }
    
    function addItem() {
        const itemId = itemCount++;
        
        const productOptions = products.map(p => `
            <option value="${p.id}" data-cost="${p.cost_price}" data-unit-price="${p.unit_price}" data-name="${p.name}">${p.name}</option>
        `).join('');
        
        const row = document.createElement('tr');
        row.className = 'hover:bg-amber-50 transition-colors new-item-row';
        row.id = `item-row-${itemId}`;
        row.innerHTML = `
            <td class="px-3 py-2">
                <select name="items[${itemId}][product_id]" class="product-select-${itemId}" data-id="${itemId}" style="width: 100%;" required>
                    <option value="">Select product...</option>
                    ${productOptions}
                </select>
                <div class="recommendation-container mt-2" id="recommendation-${itemId}"></div>
             </div>
            <td class="px-3 py-2">
                <input type="number" name="items[${itemId}][quantity]" class="quantity-input w-20 text-center rounded border-gray-200 focus:border-amber-500 focus:ring-amber-500" value="1" min="1" step="1" data-id="${itemId}" required>
             </div>
            <td class="px-3 py-2">
                <span class="current-cost text-sm text-gray-600 font-mono" id="current-cost-${itemId}">0.00</span>
             </div>
            <td class="px-3 py-2">
                <input type="number" name="items[${itemId}][cost_price]" class="cost-input w-28 text-right rounded border-gray-200 focus:border-amber-500 focus:ring-amber-500" value="0" step="0.01" data-id="${itemId}" required placeholder="Purchase price">
                <div class="cost-comparison text-xs mt-1" id="cost-compare-${itemId}"></div>
             </div>
            <td class="px-3 py-2">
                <span class="current-unit text-sm text-gray-600 font-mono" id="current-unit-${itemId}">0.00</span>
             </div>
            <td class="px-3 py-2">
                <input type="number" name="items[${itemId}][unit_price]" class="unit-price-input w-28 text-right rounded border-gray-200 focus:border-amber-500 focus:ring-amber-500" value="0" step="0.01" data-id="${itemId}" required placeholder="Selling price">
                <div class="unit-comparison text-xs mt-1" id="unit-compare-${itemId}"></div>
             </div>
            <td class="px-3 py-2 text-right">
                <span class="item-total font-semibold" data-id="${itemId}">GHS 0.00</span>
             </div>
            <td class="px-3 py-2 text-center">
                <button type="button" onclick="removeItem(${itemId})" class="text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
             </div>
        `;
        
        document.getElementById('items-body').appendChild(row);
        
        // Initialize Select2
        $(`.product-select-${itemId}`).select2({
            placeholder: "Search for a product...",
            allowClear: true,
            width: '100%',
            dropdownParent: $(`#item-row-${itemId}`)
        }).on('change', function() {
            updateItemFromProduct(itemId);
        });
        
        // Attach event listeners
        const quantity = document.querySelector(`input[name="items[${itemId}][quantity]"]`);
        const costPrice = document.querySelector(`input[name="items[${itemId}][cost_price]"]`);
        const unitPrice = document.querySelector(`input[name="items[${itemId}][unit_price]"]`);
        
        if (quantity) quantity.addEventListener('input', () => updateItemTotal(itemId));
        if (costPrice) costPrice.addEventListener('input', () => {
            updateItemTotal(itemId);
            updatePriceComparison(itemId, 'cost');
            updateRecommendation(itemId);
        });
        if (unitPrice) unitPrice.addEventListener('input', () => {
            updatePriceComparison(itemId, 'unit');
            updateRecommendation(itemId);
        });
        
        updateSubmitButton();
    }
    
    function updateItemFromProduct(itemId) {
        const select = document.querySelector(`select[data-id="${itemId}"]`);
        if (!select || !select.value) return;
        
        const selectedOption = select.options[select.selectedIndex];
        const currentCost = parseFloat(selectedOption?.dataset?.cost) || 0;
        const currentUnit = parseFloat(selectedOption?.dataset?.unitPrice) || 0;
        
        document.getElementById(`current-cost-${itemId}`).innerText = currentCost.toFixed(2);
        document.getElementById(`current-unit-${itemId}`).innerText = currentUnit.toFixed(2);
        
        document.querySelector(`input[name="items[${itemId}][cost_price]"]`).value = currentCost;
        document.querySelector(`input[name="items[${itemId}][unit_price]"]`).value = currentUnit;
        
        updatePriceComparison(itemId, 'cost');
        updatePriceComparison(itemId, 'unit');
        updateRecommendation(itemId);
        updateItemTotal(itemId);
    }
    
    function updatePriceComparison(itemId, type) {
        const select = document.querySelector(`select[data-id="${itemId}"]`);
        if (!select || !select.value) return;
        
        const selectedOption = select.options[select.selectedIndex];
        const currentPrice = type === 'cost' ? parseFloat(selectedOption?.dataset?.cost) || 0 : parseFloat(selectedOption?.dataset?.unitPrice) || 0;
        const newPriceInput = document.querySelector(`input[name="items[${itemId}][${type === 'cost' ? 'cost_price' : 'unit_price'}"]`);
        const newPrice = parseFloat(newPriceInput?.value) || 0;
        
        const comparisonDiv = document.getElementById(`${type === 'cost' ? 'cost-compare' : 'unit-compare'}-${itemId}`);
        
        if (comparisonDiv && currentPrice > 0 && newPrice > 0) {
            const difference = newPrice - currentPrice;
            const percentChange = (difference / currentPrice) * 100;
            
            if (difference > 0) {
                comparisonDiv.innerHTML = `<span class="price-increase">↑ +${difference.toFixed(2)} (${percentChange.toFixed(1)}% ↑)</span>`;
            } else if (difference < 0) {
                comparisonDiv.innerHTML = `<span class="price-decrease">↓ ${difference.toFixed(2)} (${Math.abs(percentChange).toFixed(1)}% ↓)</span>`;
            } else {
                comparisonDiv.innerHTML = `<span class="price-same">→ No change</span>`;
            }
        } else if (comparisonDiv) {
            comparisonDiv.innerHTML = '';
        }
    }
    
    function updateRecommendation(itemId) {
        const select = document.querySelector(`select[data-id="${itemId}"]`);
        if (!select || !select.value) return;
        
        const selectedOption = select.options[select.selectedIndex];
        const currentCost = parseFloat(selectedOption?.dataset?.cost) || 0;
        const currentUnit = parseFloat(selectedOption?.dataset?.unitPrice) || 0;
        
        const newCost = parseFloat(document.querySelector(`input[name="items[${itemId}][cost_price]"]`)?.value) || 0;
        const newUnit = parseFloat(document.querySelector(`input[name="items[${itemId}][unit_price]"]`)?.value) || 0;
        
        const recommendationDiv = document.getElementById(`recommendation-${itemId}`);
        
        if (newCost > 0 && newUnit > 0) {
            const recommendation = getRecommendationMessage(selectedOption.dataset.name, currentCost, newCost, currentUnit, newUnit);
            
            const icon = recommendation.type === 'up' ? '📈' : (recommendation.type === 'down' ? '📉' : '💡');
            const bgClass = `recommendation-${recommendation.type === 'up' ? 'up' : (recommendation.type === 'down' ? 'down' : 'hold')}`;
            
            recommendationDiv.innerHTML = `
                <div class="recommendation-badge ${bgClass} alert-pulse">
                    <span>${icon}</span>
                    <span>${recommendation.message}</span>
                </div>
            `;
            
            setTimeout(() => {
                const badge = recommendationDiv.querySelector('.alert-pulse');
                if (badge) badge.classList.remove('alert-pulse');
            }, 2000);
        } else {
            recommendationDiv.innerHTML = '';
        }
    }
    
    function updateItemTotal(itemId) {
        const quantity = parseInt(document.querySelector(`input[name="items[${itemId}][quantity]"]`)?.value) || 0;
        const costPrice = parseFloat(document.querySelector(`input[name="items[${itemId}][cost_price]"]`)?.value) || 0;
        const total = quantity * costPrice;
        
        const totalSpan = document.querySelector(`.item-total[data-id="${itemId}"]`);
        if (totalSpan) {
            totalSpan.innerText = `GHS ${total.toFixed(2)}`;
        }
        
        updateTotals();
    }
    
    function removeItem(itemId) {
        const row = document.getElementById(`item-row-${itemId}`);
        if (row) {
            const select = row.querySelector('select');
            if (select && $(select).data('select2')) {
                $(select).select2('destroy');
            }
            row.remove();
            updateTotals();
            updateSubmitButton();
        }
    }
    
    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item-total').forEach(el => {
            const total = parseFloat(el.innerText.replace('GHS ', ''));
            if (!isNaN(total)) {
                subtotal += total;
            }
        });
        
        // NO TAX - supplier already includes tax in cost price
        const total = subtotal;
        
        document.getElementById('subtotal').innerHTML = `GHS ${subtotal.toFixed(2)}`;
        document.getElementById('total').innerHTML = `GHS ${total.toFixed(2)}`;
    }
    
    // Form validation
    document.getElementById('purchaseForm').addEventListener('submit', function(e) {
        const itemsCount = document.querySelectorAll('#items-body tr').length;
        if (itemsCount === 0) {
            e.preventDefault();
            alert('Please add at least one item to the purchase order');
            return;
        }
        
        let hasProduct = true;
        document.querySelectorAll('#items-body select').forEach(select => {
            if (!select.value) {
                hasProduct = false;
            }
        });
        
        if (!hasProduct) {
            e.preventDefault();
            alert('Please select a product for each item');
            return;
        }
        
        let hasCostPrice = true;
        document.querySelectorAll('.cost-input').forEach(input => {
            const cost = parseFloat(input.value) || 0;
            if (cost <= 0) {
                hasCostPrice = false;
            }
        });
        
        if (!hasCostPrice) {
            e.preventDefault();
            alert('Please ensure all items have a valid purchase price (cost price)');
            return;
        }
        
        let hasUnitPrice = true;
        document.querySelectorAll('.unit-price-input').forEach(input => {
            const unit = parseFloat(input.value) || 0;
            if (unit <= 0) {
                hasUnitPrice = false;
            }
        });
        
        if (!hasUnitPrice) {
            e.preventDefault();
            alert('Please ensure all items have a valid selling price (unit price)');
            return;
        }
    });
    
    // Add first item
    $(document).ready(function() {
        addItem();
    });
</script>
@endpush
@endsection