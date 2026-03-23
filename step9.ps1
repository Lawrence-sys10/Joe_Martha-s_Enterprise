# Step9-BladeViews.ps1
# Run this script to create all remaining blade views

Write-Host "Step 9: Creating Blade Views for All Modules..." -ForegroundColor Green

# Create Products Index View
@'
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Products') }}
            </h2>
            @can('create products')
            <a href="{{ route('products.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">
                + Add Product
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <!-- Search and Filter -->
                    <div class="mb-6">
                        <form method="GET" class="flex gap-4">
                            <input type="text" name="search" placeholder="Search by name, SKU or barcode..." 
                                   value="{{ request('search') }}" 
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <select name="category_id" class="rounded-md border-gray-300">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            <select name="is_active" class="rounded-md border-gray-300">
                                <option value="">All Status</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Filter</button>
                            <a href="{{ route('products.index') }}" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">Reset</a>
                        </form>
                    </div>

                    <!-- Products Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($products as $product)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $product->barcode }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $product->sku }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $product->category->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-right text-gray-900">GHS {{ number_format($product->unit_price, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-right">
                                        <span class="{{ $product->isLowStock() ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                            {{ $product->stock_quantity }} {{ $product->unit }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        @can('edit products')
                                        <a href="{{ route('products.edit', $product) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                        @endcan
                                        @can('delete products')
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No products found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $products->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
'@ | Out-File -FilePath "resources\views\products\index.blade.php" -Encoding UTF8 -NoNewline

# Create POS (Sales Create) View
@'
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Point of Sale (POS)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Product Search and Selection -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Select Products</h3>
                        
                        <!-- Search -->
                        <div class="mb-4">
                            <input type="text" id="product-search" placeholder="Search by name, SKU or barcode..." 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        
                        <!-- Products Grid -->
                        <div id="products-grid" class="grid grid-cols-2 md:grid-cols-3 gap-4 max-h-96 overflow-y-auto">
                            @foreach($products as $product)
                            <div class="product-item border rounded-lg p-3 cursor-pointer hover:bg-gray-50" 
                                 data-id="{{ $product->id }}"
                                 data-name="{{ $product->name }}"
                                 data-price="{{ $product->unit_price }}"
                                 data-stock="{{ $product->stock_quantity }}">
                                <div class="font-semibold">{{ $product->name }}</div>
                                <div class="text-sm text-gray-600">GHS {{ number_format($product->unit_price, 2) }}</div>
                                <div class="text-xs text-gray-400">Stock: {{ $product->stock_quantity }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Cart -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Shopping Cart</h3>
                        
                        <div id="cart-items" class="max-h-96 overflow-y-auto mb-4">
                            <div class="text-center text-gray-500 py-8">No items in cart</div>
                        </div>
                        
                        <div class="border-t pt-4">
                            <div class="flex justify-between mb-2">
                                <span>Subtotal:</span>
                                <span id="subtotal">GHS 0.00</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span>Tax (12.5%):</span>
                                <span id="tax">GHS 0.00</span>
                            </div>
                            <div class="flex justify-between mb-4 font-bold">
                                <span>Total:</span>
                                <span id="total">GHS 0.00</span>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                                <select id="payment-method" class="w-full rounded-md border-gray-300">
                                    <option value="cash">Cash</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="bank">Bank Transfer</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Amount Paid</label>
                                <input type="number" id="amount-paid" class="w-full rounded-md border-gray-300" value="0">
                            </div>
                            
                            <div class="mb-4">
                                <span class="text-sm">Change: </span>
                                <span id="change" class="font-bold">GHS 0.00</span>
                            </div>
                            
                            <button id="checkout-btn" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                                Complete Sale
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        let cart = [];
        
        // Add to cart
        document.querySelectorAll('.product-item').forEach(item => {
            item.addEventListener('click', () => {
                const id = item.dataset.id;
                const name = item.dataset.name;
                const price = parseFloat(item.dataset.price);
                const stock = parseInt(item.dataset.stock);
                
                const existingItem = cart.find(i => i.id === id);
                if (existingItem) {
                    if (existingItem.quantity + 1 <= stock) {
                        existingItem.quantity++;
                    } else {
                        alert('Insufficient stock!');
                    }
                } else {
                    cart.push({ id, name, price, quantity: 1 });
                }
                updateCart();
            });
        });
        
        function updateCart() {
            const cartContainer = document.getElementById('cart-items');
            if (cart.length === 0) {
                cartContainer.innerHTML = '<div class="text-center text-gray-500 py-8">No items in cart</div>';
            } else {
                cartContainer.innerHTML = '';
                cart.forEach((item, index) => {
                    cartContainer.innerHTML += `
                        <div class="flex justify-between items-center mb-2 p-2 border rounded">
                            <div>
                                <div class="font-semibold">${item.name}</div>
                                <div class="text-sm">GHS ${item.price.toFixed(2)} x ${item.quantity}</div>
                            </div>
                            <div>
                                <button onclick="updateQuantity(${index}, -1)" class="text-red-600 px-2">-</button>
                                <span>${item.quantity}</span>
                                <button onclick="updateQuantity(${index}, 1)" class="text-green-600 px-2">+</button>
                                <button onclick="removeItem(${index})" class="text-red-600 ml-2">×</button>
                            </div>
                        </div>
                    `;
                });
            }
            
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const tax = subtotal * 0.125;
            const total = subtotal + tax;
            
            document.getElementById('subtotal').innerText = `GHS ${subtotal.toFixed(2)}`;
            document.getElementById('tax').innerText = `GHS ${tax.toFixed(2)}`;
            document.getElementById('total').innerText = `GHS ${total.toFixed(2)}`;
            
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
            document.getElementById('change').innerText = `GHS ${change >= 0 ? change.toFixed(2) : '0.00'}`;
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
            
            const saleData = {
                payment_method: document.getElementById('payment-method').value,
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
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(saleData)
                });
                
                const result = await response.json();
                if (result.success) {
                    alert(`Sale completed! Invoice: ${result.invoice_number}`);
                    cart = [];
                    updateCart();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error processing sale: ' + error.message);
            }
        });
        
        // Product search
        document.getElementById('product-search').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                const name = item.dataset.name.toLowerCase();
                if (name.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
'@ | Out-File -FilePath "resources\views\sales\create.blade.php" -Encoding UTF8 -NoNewline

# Create Sales Index View
@'
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sales Transactions') }}
            </h2>
            @can('access pos')
            <a href="{{ route('pos.index') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                New Sale
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <!-- Filters -->
                    <div class="mb-6">
                        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-md border-gray-300">
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-md border-gray-300">
                            <select name="status" class="rounded-md border-gray-300">
                                <option value="">All Status</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Filter</button>
                        </form>
                    </div>

                    <!-- Sales Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($sales as $sale)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $sale->invoice_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $sale->customer->name ?? 'Walk-in Customer' }}</td>
                                    <td class="px-6 py-4 text-sm text-right font-semibold">GHS {{ number_format($sale->total, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sale->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        <a href="{{ route('sales.receipt', $sale) }}" class="text-green-600 hover:text-green-900">Receipt</a>
                                        @if($sale->status == 'completed')
                                        <button onclick="voidSale({{ $sale->id }})" class="text-red-600 hover:text-red-900 ml-3">Void</button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No sales found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $sales->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function voidSale(saleId) {
            const reason = prompt('Please provide a reason for voiding this sale:');
            if (reason) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/sales/${saleId}/void`;
                form.innerHTML = `
                    @csrf
                    <input type="hidden" name="reason" value="${reason}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    @endpush
</x-app-layout>
'@ | Out-File -FilePath "resources\views\sales\index.blade.php" -Encoding UTF8 -NoNewline

Write-Host "Step 9 Complete: Blade Views created!" -ForegroundColor Green
Write-Host ""
Write-Host "Created views:" -ForegroundColor Yellow
Write-Host "  - products/index.blade.php: Product listing with filters" -ForegroundColor Green
Write-Host "  - sales/create.blade.php: POS interface with cart" -ForegroundColor Green
Write-Host "  - sales/index.blade.php: Sales transactions list" -ForegroundColor Green
Write-Host ""
Write-Host "Type 'next' to proceed to Step 10: Final Configuration and Testing" -ForegroundColor Yellow