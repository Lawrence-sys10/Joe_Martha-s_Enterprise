<?php
// setup_low_stock.php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Product;
use App\Models\Category;

echo "=========================================\n";
echo "Low Stock Notification Setup\n";
echo "=========================================\n\n";

// Step 1: Update admin user with phone number
echo "Step 1: Updating admin user...\n";
$admin = User::where('email', 'admin@jm-ems.com')->first();
if ($admin) {
    $admin->phone = '233593001501';
    $admin->save();
    echo "✓ Admin user updated!\n";
    echo "  Name: {$admin->name}\n";
    echo "  Email: {$admin->email}\n";
    echo "  Phone: {$admin->phone}\n\n";
} else {
    echo "✗ Admin user not found!\n\n";
}

// Step 2: Create a product with low stock
echo "Step 2: Creating low stock product...\n";
$product = Product::where('name', 'Test Low Stock Item')->first();

if (!$product) {
    $category = Category::first();
    $product = Product::create([
        'name' => 'Test Low Stock Item',
        'sku' => 'LOW001',
        'unit_price' => 10.00,
        'cost_price' => 5.00,
        'stock_quantity' => 1,
        'minimum_stock' => 10,
        'unit' => 'piece',
        'category_id' => $category ? $category->id : null,
        'is_active' => true,
        'tax_rate' => 12.5,
        'description' => 'Test product for low stock notification'
    ]);
    echo "✓ Created new product: {$product->name}\n";
} else {
    $product->stock_quantity = 1;
    $product->minimum_stock = 10;
    $product->save();
    echo "✓ Updated existing product: {$product->name}\n";
}
echo "  Current Stock: {$product->stock_quantity}\n";
echo "  Minimum Stock: {$product->minimum_stock}\n";
echo "  Status: " . ($product->stock_quantity <= $product->minimum_stock ? "⚠️ LOW STOCK!" : "OK") . "\n\n";

// Step 3: Show all low stock products
echo "Step 3: Current low stock products:\n";
$lowStock = Product::whereRaw('stock_quantity <= minimum_stock')
    ->where('stock_quantity', '>', 0)
    ->where('is_active', true)
    ->get();

if ($lowStock->count() > 0) {
    foreach ($lowStock as $p) {
        echo "  - {$p->name}: Stock {$p->stock_quantity} / Min {$p->minimum_stock}\n";
    }
} else {
    echo "  No low stock products found.\n";
}

echo "\n=========================================\n";
echo "Setup Complete!\n";
echo "=========================================\n";
echo "\nNow run: php artisan stock:check-low\n";
echo "This will send notifications to: {$admin->email} and {$admin->phone}\n";