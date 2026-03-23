<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class CategoriesAndProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Check if categories already exist
        if (Category::count() > 0) {
            $this->command->info('Categories and products already exist, skipping...');
            return;
        }
        
        // Create categories
        $categories = [
            ['name' => 'Beverages', 'description' => 'Soft drinks, juices, water, and energy drinks', 'is_active' => true],
            ['name' => 'Food Items', 'description' => 'Canned food, snacks, noodles, and groceries', 'is_active' => true],
            ['name' => 'Household', 'description' => 'Cleaning products, detergents, and household items', 'is_active' => true],
            ['name' => 'Personal Care', 'description' => 'Toiletries, cosmetics, and personal hygiene', 'is_active' => true],
            ['name' => 'Stationery', 'description' => 'Books, pens, and office supplies', 'is_active' => true],
            ['name' => 'Electronics', 'description' => 'Batteries, chargers, and small electronics', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create products
        $products = [
            [
                'name' => 'Coca Cola 50cl',
                'sku' => 'CC001',
                'unit_price' => 5.00,
                'cost_price' => 3.50,
                'stock_quantity' => 100,
                'minimum_stock' => 20,
                'unit' => 'bottle',
                'category_id' => 1,
                'is_active' => true,
                'tax_rate' => 12.5,
            ],
            [
                'name' => 'FanIce Yogurt',
                'sku' => 'FI001',
                'unit_price' => 3.00,
                'cost_price' => 2.00,
                'stock_quantity' => 150,
                'minimum_stock' => 30,
                'unit' => 'piece',
                'category_id' => 1,
                'is_active' => true,
                'tax_rate' => 12.5,
            ],
            [
                'name' => 'Indomie Noodles',
                'sku' => 'IN001',
                'unit_price' => 2.50,
                'cost_price' => 1.80,
                'stock_quantity' => 200,
                'minimum_stock' => 50,
                'unit' => 'packet',
                'category_id' => 2,
                'is_active' => true,
                'tax_rate' => 5,
            ],
            [
                'name' => 'OMO Washing Powder 500g',
                'sku' => 'OW001',
                'unit_price' => 15.00,
                'cost_price' => 10.00,
                'stock_quantity' => 50,
                'minimum_stock' => 10,
                'unit' => 'packet',
                'category_id' => 3,
                'is_active' => true,
                'tax_rate' => 12.5,
            ],
            [
                'name' => 'Key Soap',
                'sku' => 'KS001',
                'unit_price' => 8.00,
                'cost_price' => 5.50,
                'stock_quantity' => 80,
                'minimum_stock' => 20,
                'unit' => 'piece',
                'category_id' => 4,
                'is_active' => true,
                'tax_rate' => 5,
            ],
            [
                'name' => 'A4 Paper 80gsm',
                'sku' => 'AP001',
                'unit_price' => 25.00,
                'cost_price' => 18.00,
                'stock_quantity' => 30,
                'minimum_stock' => 10,
                'unit' => 'ream',
                'category_id' => 5,
                'is_active' => true,
                'tax_rate' => 5,
            ],
            [
                'name' => 'Energizer AA Batteries',
                'sku' => 'EB001',
                'unit_price' => 12.00,
                'cost_price' => 8.00,
                'stock_quantity' => 60,
                'minimum_stock' => 15,
                'unit' => 'pack',
                'category_id' => 6,
                'is_active' => true,
                'tax_rate' => 12.5,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Categories and products created successfully!');
    }
}