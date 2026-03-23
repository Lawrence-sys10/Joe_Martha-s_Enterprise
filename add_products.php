<?php
// add_products.php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Category;

echo "Adding products...\n\n";

// Get category IDs
$beverages = Category::where('name', 'Beverages')->first();
$foodItems = Category::where('name', 'Food Items')->first();
$household = Category::where('name', 'Household')->first();
$personalCare = Category::where('name', 'Personal Care')->first();
$stationery = Category::where('name', 'Stationery')->first();
$electronics = Category::where('name', 'Electronics')->first();

$products = [
    // Beverages - More drinks
    [
        'name' => 'Sprite 50cl',
        'sku' => 'SPR001',
        'unit_price' => 5.00,
        'cost_price' => 3.50,
        'stock_quantity' => 85,
        'minimum_stock' => 20,
        'unit' => 'bottle',
        'category_id' => $beverages->id,
        'is_active' => true,
        'tax_rate' => 12.5,
        'description' => 'Refreshing lemon-lime soda',
    ],
    [
        'name' => 'Fanta Orange 50cl',
        'sku' => 'FAN001',
        'unit_price' => 5.00,
        'cost_price' => 3.50,
        'stock_quantity' => 92,
        'minimum_stock' => 20,
        'unit' => 'bottle',
        'category_id' => $beverages->id,
        'is_active' => true,
        'tax_rate' => 12.5,
        'description' => 'Orange flavored soda',
    ],
    [
        'name' => 'Milo 400g',
        'sku' => 'MIL001',
        'unit_price' => 25.00,
        'cost_price' => 18.00,
        'stock_quantity' => 45,
        'minimum_stock' => 10,
        'unit' => 'tin',
        'category_id' => $beverages->id,
        'is_active' => true,
        'tax_rate' => 5,
        'description' => 'Chocolate malt drink',
    ],
    [
        'name' => 'Bournvita 400g',
        'sku' => 'BOU001',
        'unit_price' => 24.00,
        'cost_price' => 17.00,
        'stock_quantity' => 38,
        'minimum_stock' => 10,
        'unit' => 'tin',
        'category_id' => $beverages->id,
        'is_active' => true,
        'tax_rate' => 5,
        'description' => 'Rich chocolate malt drink',
    ],
    [
        'name' => 'Voltic Water 1.5L',
        'sku' => 'VOL001',
        'unit_price' => 3.00,
        'cost_price' => 2.00,
        'stock_quantity' => 120,
        'minimum_stock' => 30,
        'unit' => 'bottle',
        'category_id' => $beverages->id,
        'is_active' => true,
        'tax_rate' => 0,
        'description' => 'Pure drinking water',
    ],
    
    // More Food Items
    [
        'name' => 'Gino Tomato Mix 400g',
        'sku' => 'GIN001',
        'unit_price' => 8.00,
        'cost_price' => 5.50,
        'stock_quantity' => 75,
        'minimum_stock' => 15,
        'unit' => 'tin',
        'category_id' => $foodItems->id,
        'is_active' => true,
        'tax_rate' => 5,
        'description' => 'Tomato paste for cooking',
    ],
    [
        'name' => 'Mackerel Sardines 125g',
        'sku' => 'MAC001',
        'unit_price' => 12.00,
        'cost_price' => 8.50,
        'stock_quantity' => 60,
        'minimum_stock' => 20,
        'unit' => 'tin',
        'category_id' => $foodItems->id,
        'is_active' => true,
        'tax_rate' => 5,
        'description' => 'Premium sardines in oil',
    ],
    [
        'name' => 'Gari 1kg',
        'sku' => 'GAR001',
        'unit_price' => 15.00,
        'cost_price' => 10.00,
        'stock_quantity' => 50,
        'minimum_stock' => 15,
        'unit' => 'bag',
        'category_id' => $foodItems->id,
        'is_active' => true,
        'tax_rate' => 0,
        'description' => 'Fresh cassava gari',
    ],
    [
        'name' => 'Rice 5kg',
        'sku' => 'RIC001',
        'unit_price' => 45.00,
        'cost_price' => 35.00,
        'stock_quantity' => 30,
        'minimum_stock' => 10,
        'unit' => 'bag',
        'category_id' => $foodItems->id,
        'is_active' => true,
        'tax_rate' => 0,
        'description' => 'Premium long grain rice',
    ],
    [
        'name' => 'Sugar 1kg',
        'sku' => 'SUG001',
        'unit_price' => 12.00,
        'cost_price' => 8.00,
        'stock_quantity' => 85,
        'minimum_stock' => 20,
        'unit' => 'packet',
        'category_id' => $foodItems->id,
        'is_active' => true,
        'tax_rate' => 5,
        'description' => 'White granulated sugar',
    ],
    [
        'name' => 'Cooking Oil 2L',
        'sku' => 'OIL001',
        'unit_price' => 35.00,
        'cost_price' => 25.00,
        'stock_quantity' => 40,
        'minimum_stock' => 10,
        'unit' => 'bottle',
        'category_id' => $foodItems->id,
        'is_active' => true,
        'tax_rate' => 5,
        'description' => 'Vegetable cooking oil',
    ],
    
    // More Household Items
    [
        'name' => 'Bleach 1L',
        'sku' => 'BLE001',
        'unit_price' => 10.00,
        'cost_price' => 6.00,
        'stock_quantity' => 55,
        'minimum_stock' => 15,
        'unit' => 'bottle',
        'category_id' => $household->id,
        'is_active' => true,
        'tax_rate' => 12.5,
        'description' => 'Laundry bleach',
    ],
    [
        'name' => 'Air Freshener',
        'sku' => 'AIR001',
        'unit_price' => 18.00,
        'cost_price' => 12.00,
        'stock_quantity' => 42,
        'minimum_stock' => 10,
        'unit' => 'spray',
        'category_id' => $household->id,
        'is_active' => true,
        'tax_rate' => 12.5,
        'description' => 'Room freshener spray',
    ],
    [
        'name' => 'Insecticide Spray',
        'sku' => 'INS001',
        'unit_price' => 25.00,
        'cost_price' => 18.00,
        'stock_quantity' => 35,
        'minimum_stock' => 10,
        'unit' => 'can',
        'category_id' => $household->id,
        'is_active' => true,
        'tax_rate' => 12.5,
        'description' => 'Mosquito and insect killer',
    ],
    [
        'name' => 'Dishwashing Liquid 500ml',
        'sku' => 'DIS001',
        'unit_price' => 8.00,
        'cost_price' => 4.50,
        'stock_quantity' => 70,
        'minimum_stock' => 20,
        'unit' => 'bottle',
        'category_id' => $household->id,
        'is_active' => true,
        'tax_rate' => 12.5,
        'description' => 'Kitchen dish soap',
    ],
    
    // More Personal Care
    [
        'name' => 'Toothpaste 100g',
        'sku' => 'TOO001',
        'unit_price' => 12.00,
        'cost_price' => 7.00,
        'stock_quantity' => 90,
        'minimum_stock' => 25,
        'unit' => 'tube',
        'category_id' => $personalCare->id,
        'is_active' => true,
        'tax_rate' => 5,
        'description' => 'Fluoride toothpaste',
    ],
    [
        'name' => 'Body Cream 200ml',
        'sku' => 'BOD001',
        'unit_price' => 22.00,
        'cost_price' => 14.00,
        'stock_quantity' => 48,
        'minimum_stock' => 15,
        'unit' => 'jar',
        'category_id' => $personalCare->id,
        'is_active' => true,
        'tax_rate' => 5,
        'description' => 'Moisturizing body cream',
    ],
    [
        'name' => 'Shampoo 250ml',
        'sku' => 'SHA001',
        'unit_price' => 18.00,
        'cost_price' => 11.00,
        'stock_quantity' => 52,
        'minimum_stock' => 15,
        'unit' => 'bottle',
        'category_id' => $personalCare->id,
        'is_active' => true,
        'tax_rate' => 5,
        'description' => 'Hair shampoo',
    ],
    [
        'name' => 'Deodorant',
        'sku' => 'DEO001',
        'unit_price' => 15.00,
        'cost_price' => 9.00,
        'stock_quantity' => 60,
        'minimum_stock' => 20,
        'unit' => 'stick',
        'category_id' => $personalCare->id,
        'is_active' => true,
        'tax_rate' => 12.5,
        'description' => 'Men\'s deodorant stick',
    ],
    
    // More Stationery
    [
        'name' => 'Ballpoint Pen Blue',
        'sku' => 'PEN001',
        'unit_price' => 1.50,
        'cost_price' => 0.80,
        'stock_quantity' => 200,
        'minimum_stock' => 50,
        'unit' => 'piece',
        'category_id' => $stationery->id,
        'is_active' => true,
        'tax_rate' => 5,
        'description' => 'Blue ink ballpoint pen',
    ],
    [
        'name' => 'Exercise Book 80pg',
        'sku' => 'EXE001',
        'unit_price' => 5.00,
        'cost_price' => 3.00,
        'stock_quantity' => 150,
        'minimum_stock' => 40,
        'unit' => 'book',
        'category_id' => $stationery->id,
        'is_active' => true,
        'tax_rate' => 0,
        'description' => '80-page exercise book',
    ],
    
    // More Electronics
    [
        'name' => 'Phone Charger',
        'sku' => 'CHA001',
        'unit_price' => 35.00,
        'cost_price' => 22.00,
        'stock_quantity' => 25,
        'minimum_stock' => 8,
        'unit' => 'piece',
        'category_id' => $electronics->id,
        'is_active' => true,
        'tax_rate' => 12.5,
        'description' => 'USB phone charger',
    ],
    [
        'name' => 'Earphones',
        'sku' => 'EAR001',
        'unit_price' => 20.00,
        'cost_price' => 12.00,
        'stock_quantity' => 30,
        'minimum_stock' => 10,
        'unit' => 'pair',
        'category_id' => $electronics->id,
        'is_active' => true,
        'tax_rate' => 12.5,
        'description' => 'Wired earphones',
    ],
    [
        'name' => 'Flashlight',
        'sku' => 'FLA001',
        'unit_price' => 25.00,
        'cost_price' => 15.00,
        'stock_quantity' => 35,
        'minimum_stock' => 10,
        'unit' => 'piece',
        'category_id' => $electronics->id,
        'is_active' => true,
        'tax_rate' => 5,
        'description' => 'LED flashlight with batteries',
    ],
];

$count = 0;
foreach ($products as $product) {
    if (!Product::where('sku', $product['sku'])->exists()) {
        Product::create($product);
        $count++;
        echo "Added: " . $product['name'] . "\n";
    }
}

echo "\n================================\n";
echo "✅ Added " . $count . " new products!\n";
echo "📦 Total products now: " . Product::count() . "\n";
echo "================================\n";

// Show category breakdown
echo "\n📊 Category Breakdown:\n";
foreach (Category::all() as $category) {
    echo "  - " . $category->name . ": " . $category->products()->count() . " products\n";
}

echo "\n⚠️  Low Stock Items: " . Product::whereRaw('stock_quantity <= minimum_stock')->count() . "\n";
echo "🚫 Out of Stock: " . Product::where('stock_quantity', 0)->count() . "\n";