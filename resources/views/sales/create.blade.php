@extends('layouts.app')

@section('title', 'Point of Sale - Modern POS')

@section('header')
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-white">Point of Sale</h2>
                    <p class="text-amber-100 text-sm mt-1">Fast checkout with modern interface</p>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                    <span class="text-white font-semibold">{{ now()->format('l, F j, Y') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Product Selection Area -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-amber-100 overflow-hidden">
                    <div class="p-6">
                        <!-- Search Bar -->
                        <div class="mb-6">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" id="product-search" placeholder="Search products by name, SKU or barcode..." 
                                       class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                            </div>
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
                            <button class="category-filter active px-4 py-2 rounded-lg bg-amber-500 text-white font-medium whitespace-nowrap" data-category="all">All Products</button>
                            @php
                                $categories = \App\Models\Category::where('is_active', true)->get();
                            @endphp
                            @foreach($categories as $cat)
                            <button class="category-filter px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-amber-100 hover:text-amber-700 transition-all whitespace-nowrap" data-category="{{ $cat->id }}">
                                {{ $cat->name }}
                            </button>
                            @endforeach
                        </div>
                        
                        <!-- Products Grid -->
                        <div id="products-grid" class="grid grid-cols-2 md:grid-cols-3 gap-4 max-h-[500px] overflow-y-auto pr-2">
                            @foreach($products as $product)
                            <div class="product-item bg-gradient-to-br from-white to-gray-50 rounded-xl border-2 border-gray-100 hover:border-amber-300 hover:shadow-lg transition-all duration-200 overflow-hidden" 
                                 data-id="{{ $product->id }}"
                                 data-name="{{ $product->name }}"
                                 data-price="{{ $product->unit_price }}"
                                 data-stock="{{ $product->stock_quantity }}"
                                 data-category="{{ $product->category_id }}">
                                <div class="p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>
                                            <p class="text-xs text-gray-500 mt-1">SKU: {{ $product->sku }}</p>
                                        </div>
                                        @if($product->stock_quantity <= 10)
                                        <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full">Low Stock</span>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        <div class="text-2xl font-bold text-amber-600">GHS {{ number_format($product->unit_price, 2) }}</div>
                                        <div class="text-xs text-gray-500 mt-1">Stock: {{ $product->stock_quantity }} {{ $product->unit }}s</div>
                                    </div>
                                    <div class="mt-4">
                                        <button class="add-to-cart-btn w-full bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white py-2 rounded-lg text-sm font-semibold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cart Area -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl border border-amber-100 sticky top-4">
                    <div class="bg-gradient-to-r from-amber-500 to-orange-600 p-4 rounded-t-2xl">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Shopping Cart
                        </h3>
                        <p class="text-amber-100 text-xs mt-1">Items in your cart</p>
                    </div>
                    
                    <div class="p-5">
                        <!-- Cart Items -->
                        <div id="cart-items" class="max-h-[400px] overflow-y-auto space-y-3 mb-5">
                            <div class="text-center text-gray-400 py-12">
                                <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <p>Your cart is empty</p>
                                <p class="text-xs mt-1">Add items to get started</p>
                            </div>
                        </div>
                        
                        <!-- Cart Summary - Tax Row Removed -->
                        <div class="border-t border-gray-200 pt-4 space-y-2">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span id="subtotal" class="font-semibold">GHS 0.00</span>
                            </div>
                            <div class="flex justify-between text-xl font-bold pt-2 border-t border-gray-200">
                                <span>Total</span>
                                <span id="total" class="text-amber-600">GHS 0.00</span>
                            </div>
                        </div>
                        
                        <!-- Payment Section -->
                        <div class="mt-5 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button class="payment-method-btn p-2 rounded-lg border-2 border-gray-200 hover:border-amber-500 transition-all" data-method="cash">
                                        <div class="flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Cash</span>
                                        </div>
                                    </button>
                                    <button class="payment-method-btn p-2 rounded-lg border-2 border-gray-200 hover:border-amber-500 transition-all" data-method="mobile_money">
                                        <div class="flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>Mobile Money</span>
                                        </div>
                                    </button>
                                </div>
                                <input type="hidden" id="payment-method" value="cash">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Amount Paid</label>
                                <input type="number" id="amount-paid" class="w-full px-4 py-2 rounded-lg border-2 border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all" placeholder="0.00" value="0" step="0.01">
                            </div>
                            
                            <div class="bg-green-50 rounded-lg p-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-green-700">Change</span>
                                    <span id="change" class="text-xl font-bold text-green-600">GHS 0.00</span>
                                </div>
                            </div>
                            
                            <button id="checkout-btn" class="w-full bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-3 px-4 rounded-xl transition-all transform hover:scale-105 shadow-lg">
                                Complete Sale
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let cart = [];
    let selectedPaymentMethod = 'cash';
    
    // Payment method selection
    document.querySelectorAll('.payment-method-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.payment-method-btn').forEach(b => {
                b.classList.remove('border-amber-500', 'bg-amber-50');
                b.classList.add('border-gray-200');
            });
            btn.classList.remove('border-gray-200');
            btn.classList.add('border-amber-500', 'bg-amber-50');
            selectedPaymentMethod = btn.dataset.method;
            document.getElementById('payment-method').value = selectedPaymentMethod;
        });
    });
    
    // Category filtering
    document.querySelectorAll('.category-filter').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.category-filter').forEach(b => {
                b.classList.remove('bg-amber-500', 'text-white');
                b.classList.add('bg-gray-100', 'text-gray-700');
            });
            btn.classList.remove('bg-gray-100', 'text-gray-700');
            btn.classList.add('bg-amber-500', 'text-white');
            
            const category = btn.dataset.category;
            document.querySelectorAll('.product-item').forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    // Add to cart
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const productItem = btn.closest('.product-item');
            const id = parseInt(productItem.dataset.id);
            const name = productItem.dataset.name;
            const price = parseFloat(productItem.dataset.price);
            const stock = parseInt(productItem.dataset.stock);
            
            const existingItem = cart.find(i => i.id === id);
            if (existingItem) {
                if (existingItem.quantity + 1 <= stock) {
                    existingItem.quantity++;
                    showNotification(`${name} quantity increased to ${existingItem.quantity}`, 'success');
                } else {
                    alert('Insufficient stock!');
                }
            } else {
                if (stock > 0) {
                    cart.push({ id, name, price, quantity: 1 });
                    showNotification(`${name} added to cart`, 'success');
                } else {
                    alert('Product is out of stock!');
                }
            }
            updateCart();
        });
    });
    
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-y-0 opacity-100';
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }
    
    function updateCart() {
        const cartContainer = document.getElementById('cart-items');
        if (cart.length === 0) {
            cartContainer.innerHTML = `
                <div class="text-center text-gray-400 py-12">
                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <p>Your cart is empty</p>
                    <p class="text-xs mt-1">Add items to get started</p>
                </div>
            `;
        } else {
            cartContainer.innerHTML = '';
            cart.forEach((item, index) => {
                cartContainer.innerHTML += `
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:shadow-md transition-shadow">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">${item.name}</div>
                            <div class="text-sm text-gray-600 mt-1">GHS ${item.price.toFixed(2)} x ${item.quantity}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="updateQuantity(${index}, -1)" class="w-8 h-8 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">-</button>
                            <span class="w-8 text-center font-semibold">${item.quantity}</span>
                            <button onclick="updateQuantity(${index}, 1)" class="w-8 h-8 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors">+</button>
                            <button onclick="removeItem(${index})" class="ml-1 text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
            });
        }
        
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const total = subtotal;
        
        document.getElementById('subtotal').innerHTML = `GHS ${subtotal.toFixed(2)}`;
        document.getElementById('total').innerHTML = `GHS ${total.toFixed(2)}`;
        
        updateChange();
    }
    
    function updateQuantity(index, delta) {
        const newQuantity = cart[index].quantity + delta;
        if (newQuantity >= 1 && newQuantity <= 999) {
            cart[index].quantity = newQuantity;
            updateCart();
        }
    }
    
    function removeItem(index) {
        cart.splice(index, 1);
        updateCart();
    }
    
    function updateChange() {
        const amountPaid = parseFloat(document.getElementById('amount-paid').value) || 0;
        const totalText = document.getElementById('total').innerText;
        const total = parseFloat(totalText.replace('GHS ', ''));
        const change = amountPaid - total;
        const changeElement = document.getElementById('change');
        changeElement.innerHTML = `GHS ${change >= 0 ? change.toFixed(2) : '0.00'}`;
        if (change < 0) {
            changeElement.classList.add('text-red-600');
            changeElement.classList.remove('text-green-600');
        } else {
            changeElement.classList.add('text-green-600');
            changeElement.classList.remove('text-red-600');
        }
    }
    
    document.getElementById('amount-paid').addEventListener('input', updateChange);
    
    document.getElementById('checkout-btn').addEventListener('click', async () => {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }
        
        const totalText = document.getElementById('total').innerText;
        const total = parseFloat(totalText.replace('GHS ', ''));
        const amountPaid = parseFloat(document.getElementById('amount-paid').value) || 0;
        
        if (amountPaid < total) {
            alert('Insufficient payment amount!');
            return;
        }
        
        // Disable button to prevent double submission
        const checkoutBtn = document.getElementById('checkout-btn');
        checkoutBtn.disabled = true;
        checkoutBtn.innerHTML = 'Processing...';
        
        const saleData = {
            payment_method: selectedPaymentMethod,
            paid_amount: amountPaid,
            items: cart.map(item => ({
                product_id: item.id,
                quantity: item.quantity
            }))
        };
        
        try {
            const response = await fetch('{{ route("pos.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(saleData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(`✅ Sale Completed!\nInvoice: ${result.invoice_number}\nTotal: GHS ${total.toFixed(2)}`);
                window.location.href = '{{ route("sales.index") }}';
            } else {
                alert('❌ Error: ' + result.message);
                checkoutBtn.disabled = false;
                checkoutBtn.innerHTML = 'Complete Sale';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('❌ Error processing sale: ' + error.message);
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = 'Complete Sale';
        }
    });
    
    // Product search
    document.getElementById('product-search').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.querySelector('h3').textContent.toLowerCase();
            if (name.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection