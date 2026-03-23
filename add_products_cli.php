<?php
// add_products_cli.php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Adding products...\n\n";

// Get category IDs
$beverages = DB::table('categories')->where('name', 'Beverages')->value('id');
$food = DB::table('categories')->where('name', 'Food Items')->value('id');
$household = DB::table('categories')->where('name', 'Household')->value('id');
$personal = DB::table('categories')->where('name', 'Personal Care')->value('id');
$stationery = DB::table('categories')->where('name', 'Stationery')->value('id');
$electronics = DB::table('categories')->where('name', 'Electronics')->value('id');

$products = [
    ['Sprite 50cl', 'SPR001', 5.00, 3.50, 85, 20, 'bottle', $beverages, 12.5, 'Refreshing lemon-lime soda'],
    ['Fanta Orange 50cl', 'FAN001', 5.00, 3.50, 92, 20, 'bottle', $beverages, 12.5, 'Orange flavored soda'],
    ['Milo 400g', 'MIL001', 25.00, 18.00, 45, 10, 'tin', $beverages, 5, 'Chocolate malt drink'],
    ['Bournvita 400g', 'BOU001', 24.00, 17.00, 38, 10, 'tin', $beverages, 5, 'Rich chocolate malt drink'],
    ['Voltic Water 1.5L', 'VOL001', 3.00, 2.00, 120, 30, 'bottle', $beverages, 0, 'Pure drinking water'],
    ['Gino Tomato Mix 400g', 'GIN001', 8.00, 5.50, 75, 15, 'tin', $food, 5, 'Tomato paste for cooking'],
    ['Mackerel Sardines 125g', 'MAC001', 12.00, 8.50, 60, 20, 'tin', $food, 5, 'Premium sardines in oil'],
    ['Gari 1kg', 'GAR001', 15.00, 10.00, 50, 15, 'bag', $food, 0, 'Fresh cassava gari'],
    ['Rice 5kg', 'RIC001', 45.00, 35.00, 30, 10, 'bag', $food, 0, 'Premium long grain rice'],
    ['Sugar 1kg', 'SUG001', 12.00, 8.00, 85, 20, 'packet', $food, 5, 'White granulated sugar'],
    ['Cooking Oil 2L', 'OIL001', 35.00, 25.00, 40, 10, 'bottle', $food, 5, 'Vegetable cooking oil'],
    ['Bleach 1L', 'BLE001', 10.00, 6.00, 55, 15, 'bottle', $household, 12.5, 'Laundry bleach'],
    ['Air Freshener', 'AIR001', 18.00, 12.00, 42, 10, 'spray', $household, 12.5, 'Room freshener spray'],
    ['Insecticide Spray', 'INS001', 25.00, 18.00, 35, 10, 'can', $household, 12.5, 'Mosquito and insect killer'],
    ['Dishwashing Liquid 500ml', 'DIS001', 8.00, 4.50, 70, 20, 'bottle', $household, 12.5, 'Kitchen dish soap'],
    ['Toothpaste 100g', 'TOO001', 12.00, 7.00, 90, 25, 'tube', $personal, 5, 'Fluoride toothpaste'],
    ['Body Cream 200ml', 'BOD001', 22.00, 14.00, 48, 15, 'jar', $personal, 5, 'Moisturizing body cream'],
    ['Shampoo 250ml', 'SHA001', 18.00, 11.00, 52, 15, 'bottle', $personal, 5, 'Hair shampoo'],
    ['Deodorant', 'DEO001', 15.00, 9.00, 60, 20, 'stick', $personal, 12.5, 'Men\'s deodorant stick'],
    ['Ballpoint Pen Blue', 'PEN001', 1.50, 0.80, 200, 50, 'piece', $stationery, 5, 'Blue ink ballpoint pen'],
    ['Exercise Book 80pg', 'EXE001', 5.00, 3.00, 150, 40, 'book', $stationery, 0, '80-page exercise book'],
    ['Phone Charger', 'CHA001', 35.00, 22.00, 25, 8, 'piece', $electronics, 12.5, 'USB phone charger'],
    ['Earphones', 'EAR001', 20.00, 12.00, 30, 10, 'pair', $electronics, 12.5, 'Wired earphones'],
    ['Flashlight', 'FLA001', 25.00, 15.00, 35, 10, 'piece', $electronics, 5, 'LED flashlight with batteries'],
];

$count = 0;
foreach ($products as $p) {
    $exists = DB::table('products')->where('sku', $p[1])->exists();
    if (!$exists) {
        DB::table('products')->insert([
            'name' => $p[0],
            'sku' => $p[1],
            'unit_price' => $p[2],
            'cost_price' => $p[3],
            'stock_quantity' => $p[4],
            'minimum_stock' => $p[5],
            'unit' => $p[6],
            'category_id' => $p[7],
            'tax_rate' => $p[8],
            'description' => $p[9],
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $count++;
        echo "Added: " . $p[0] . "\n";
    } else {
        echo "Skipped (exists): " . $p[0] . "\n";
    }
}

echo "\n================================\n";
echo "✅ Added " . $count . " new products!\n";
echo "📦 Total products now: " . DB::table('products')->count() . "\n";
echo "================================\n";

echo "\n📊 Category Breakdown:\n";
$categories = DB::table('categories')->get();
foreach ($categories as $cat) {
    $prodCount = DB::table('products')->where('category_id', $cat->id)->count();
    echo "  - " . $cat->name . ": " . $prodCount . " products\n";
}

echo "\n⚠️  Low Stock Items: " . DB::table('products')->whereRaw('stock_quantity <= minimum_stock')->count() . "\n";
echo "🚫 Out of Stock: " . DB::table('products')->where('stock_quantity', 0)->count() . "\n";