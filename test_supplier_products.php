<?php
// test_supplier_products.php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Supplier;
use App\Models\Product;
use App\Models\SupplierProduct;

echo "========================================\n";
echo "Supplier Product Relationship Test\n";
echo "========================================\n\n";

// Get first supplier
$supplier = Supplier::first();
if (!$supplier) {
    echo "No suppliers found. Please add a supplier first.\n";
    exit;
}

echo "Supplier: {$supplier->name}\n\n";

// Get products
$products = Product::take(5)->get();

if ($products->count() > 0) {
    echo "Adding sample supplier product pricing...\n";
    
    foreach ($products as $product) {
        // Check if relationship already exists
        $exists = SupplierProduct::where('supplier_id', $supplier->id)
            ->where('product_id', $product->id)
            ->exists();
        
        if (!$exists) {
            // Create supplier product relationship with pack pricing
            $supplierProduct = SupplierProduct::create([
                'supplier_id' => $supplier->id,
                'product_id' => $product->id,
                'supplier_sku' => $product->sku . '-' . strtoupper(substr($supplier->name, 0, 3)),
                'pack_quantity' => 24,
                'pack_unit' => 'carton',
                'pack_price' => $product->unit_price * 20, // 20% discount for bulk
                'unit_price' => $product->unit_price,
                'minimum_order_quantity' => 1,
                'lead_time_days' => 7,
                'is_active' => true
            ]);
            
            echo "  ✓ Added {$product->name}: 24 {$supplierProduct->pack_unit}s @ GHS {$supplierProduct->pack_price}\n";
        } else {
            echo "  - {$product->name} already linked\n";
        }
    }
    
    echo "\n✅ Sample supplier product pricing added!\n";
    echo "Now when you create a purchase order for {$supplier->name}, you'll see pack pricing options.\n";
} else {
    echo "No products found. Please add products first.\n";
}

echo "\n========================================\n";
echo "Now visit: http://127.0.0.1:8000/purchases/create\n";
echo "Select supplier: {$supplier->name}\n";
echo "You'll see products with pack/carton pricing!\n";