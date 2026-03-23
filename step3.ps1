# Step3-Seeders.ps1
# Run this script to create database seeders

Write-Host "Step 3: Creating Database Seeders..." -ForegroundColor Green

# Create seeders directory if not exists
New-Item -ItemType Directory -Force -Path "database\seeders"

# Create Roles and Permissions Seeder
@'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Dashboard
            'view dashboard',
            
            // Product permissions
            'view products',
            'create products',
            'edit products',
            'delete products',
            'export products',
            'import products',
            
            // Category permissions
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Sale permissions
            'view sales',
            'create sales',
            'edit sales',
            'delete sales',
            'print receipts',
            'void sales',
            'refund sales',
            
            // POS permissions
            'access pos',
            'process payment',
            
            // Customer permissions
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            'manage customer credit',
            
            // Supplier permissions
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            
            // Purchase permissions
            'view purchases',
            'create purchases',
            'edit purchases',
            'delete purchases',
            
            // Expense permissions
            'view expenses',
            'create expenses',
            'edit expenses',
            'delete expenses',
            'approve expenses',
            
            // Report permissions
            'view reports',
            'export reports',
            'generate reports',
            
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage roles',
            
            // CCTV permissions
            'view cctv',
            'manage cctv',
            'view cctv logs',
            'export cctv logs',
            
            // Stock permissions
            'view stock',
            'manage stock',
            'adjust stock',
            'view stock movements',
            
            // Settings permissions
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles
        $adminRole = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $attendantRole = Role::create(['name' => 'Attendant', 'guard_name' => 'web']);
        $managerRole = Role::create(['name' => 'Manager', 'guard_name' => 'web']);

        // Assign all permissions to Admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign specific permissions to Attendant
        $attendantPermissions = [
            'view dashboard',
            'view products',
            'view sales',
            'create sales',
            'print receipts',
            'access pos',
            'process payment',
            'view customers',
            'create customers',
            'view stock',
        ];
        $attendantRole->givePermissionTo($attendantPermissions);

        // Assign permissions to Manager
        $managerPermissions = [
            'view dashboard',
            'view products',
            'view sales',
            'create sales',
            'view customers',
            'view suppliers',
            'view purchases',
            'view expenses',
            'view reports',
            'export reports',
            'generate reports',
            'view stock',
            'view stock movements',
            'view cctv',
            'view cctv logs',
        ];
        $managerRole->givePermissionTo($managerPermissions);

        $this->command->info('Roles and permissions created successfully!');
    }
}
'@ | Out-File -FilePath "database\seeders\RolesAndPermissionsSeeder.php" -Encoding UTF8 -NoNewline

# Create Users Seeder
@'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@jm-ems.com',
            'password' => Hash::make('password'),
            'phone' => '0593001501',
            'address' => 'Sunyani Technical University',
            'is_active' => true,
        ]);
        $admin->assignRole('Admin');

        // Create attendant user
        $attendant = User::create([
            'name' => 'Shop Attendant',
            'email' => 'attendant@jm-ems.com',
            'password' => Hash::make('password'),
            'phone' => '0593001502',
            'address' => 'Shop Location',
            'is_active' => true,
        ]);
        $attendant->assignRole('Attendant');

        // Create manager user
        $manager = User::create([
            'name' => 'Shop Manager',
            'email' => 'manager@jm-ems.com',
            'password' => Hash::make('password'),
            'phone' => '0593001503',
            'address' => 'Sunyani Technical University',
            'is_active' => true,
        ]);
        $manager->assignRole('Manager');

        $this->command->info('Users created successfully!');
        $this->command->info('Admin: admin@jm-ems.com / password');
        $this->command->info('Attendant: attendant@jm-ems.com / password');
        $this->command->info('Manager: manager@jm-ems.com / password');
    }
}
'@ | Out-File -FilePath "database\seeders\UsersSeeder.php" -Encoding UTF8 -NoNewline

# Create Categories and Products Seeder
@'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class CategoriesAndProductsSeeder extends Seeder
{
    public function run(): void
    {
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
'@ | Out-File -FilePath "database\seeders\CategoriesAndProductsSeeder.php" -Encoding UTF8 -NoNewline

# Create Customers and Suppliers Seeder
@'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Supplier;

class CustomersAndSuppliersSeeder extends Seeder
{
    public function run(): void
    {
        // Create customers
        $customers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '0244123456',
                'address' => 'Accra, Ghana',
                'opening_balance' => 0,
                'current_balance' => 0,
                'credit_limit' => 500,
                'is_active' => true,
                'loyalty_points' => 100,
            ],
            [
                'name' => 'Mary Mensah',
                'email' => 'mary@example.com',
                'phone' => '0244123457',
                'address' => 'Kumasi, Ghana',
                'opening_balance' => 0,
                'current_balance' => 0,
                'credit_limit' => 300,
                'is_active' => true,
                'loyalty_points' => 50,
            ],
            [
                'name' => 'Kwame Asare',
                'email' => 'kwame@example.com',
                'phone' => '0244123458',
                'address' => 'Takoradi, Ghana',
                'opening_balance' => 0,
                'current_balance' => 0,
                'credit_limit' => 400,
                'is_active' => true,
                'loyalty_points' => 75,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        // Create suppliers
        $suppliers = [
            [
                'name' => 'Ghana Distributors Ltd',
                'email' => 'info@ghanadistributors.com',
                'phone' => '0302123456',
                'address' => 'Industrial Area, Accra',
                'contact_person' => 'Kwame Mensah',
                'contact_person_phone' => '0244123457',
                'tax_number' => 'TIN123456',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_active' => true,
                'payment_terms' => 30,
            ],
            [
                'name' => 'Nestle Ghana Ltd',
                'email' => 'sales@nestle.com.gh',
                'phone' => '0302123457',
                'address' => 'Tema, Ghana',
                'contact_person' => 'Ama Serwaa',
                'contact_person_phone' => '0244123458',
                'tax_number' => 'TIN789012',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_active' => true,
                'payment_terms' => 45,
            ],
            [
                'name' => 'Unilever Ghana',
                'email' => 'orders@unilever.com.gh',
                'phone' => '0302123458',
                'address' => 'Spintex Road, Accra',
                'contact_person' => 'Yaw Boateng',
                'contact_person_phone' => '0244123459',
                'tax_number' => 'TIN345678',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_active' => true,
                'payment_terms' => 30,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        $this->command->info('Customers and suppliers created successfully!');
    }
}
'@ | Out-File -FilePath "database\seeders\CustomersAndSuppliersSeeder.php" -Encoding UTF8 -NoNewline

# Create CCTV Seeder
@'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CCTV;

class CCTVSeeder extends Seeder
{
    public function run(): void
    {
        $cameras = [
            [
                'camera_name' => 'Main Entrance',
                'camera_ip' => '192.168.1.101',
                'camera_location' => 'Main Shop Entrance',
                'stream_url' => 'rtsp://192.168.1.101:554/stream',
                'is_active' => true,
                'recording_enabled' => true,
                'motion_detection' => true,
                'notes' => 'Front door camera covering entrance and POS area',
            ],
            [
                'camera_name' => 'Stock Room',
                'camera_ip' => '192.168.1.102',
                'camera_location' => 'Inventory Storage Room',
                'stream_url' => 'rtsp://192.168.1.102:554/stream',
                'is_active' => true,
                'recording_enabled' => true,
                'motion_detection' => true,
                'notes' => 'Camera covering stock room and inventory',
            ],
            [
                'camera_name' => 'Checkout Counter',
                'camera_ip' => '192.168.1.103',
                'camera_location' => 'POS Counter',
                'stream_url' => 'rtsp://192.168.1.103:554/stream',
                'is_active' => true,
                'recording_enabled' => true,
                'motion_detection' => false,
                'notes' => 'Direct view of cashier and POS terminal',
            ],
            [
                'camera_name' => 'Back Door',
                'camera_ip' => '192.168.1.104',
                'camera_location' => 'Staff/Back Entrance',
                'stream_url' => 'rtsp://192.168.1.104:554/stream',
                'is_active' => true,
                'recording_enabled' => true,
                'motion_detection' => true,
                'notes' => 'Security camera covering back entrance',
            ],
        ];

        foreach ($cameras as $camera) {
            CCTV::create($camera);
        }

        $this->command->info('CCTV cameras created successfully!');
    }
}
'@ | Out-File -FilePath "database\seeders\CCTVSeeder.php" -Encoding UTF8 -NoNewline

# Update main DatabaseSeeder to call all seeders
@'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UsersSeeder::class,
            CategoriesAndProductsSeeder::class,
            CustomersAndSuppliersSeeder::class,
            CCTVSeeder::class,
        ]);
    }
}
'@ | Out-File -FilePath "database\seeders\DatabaseSeeder.php" -Encoding UTF8 -NoNewline

Write-Host "Step 3 Complete: All seeders have been created!" -ForegroundColor Green
Write-Host ""
Write-Host "Now run the following commands:" -ForegroundColor Yellow
Write-Host "php artisan migrate:fresh --seed" -ForegroundColor Cyan
Write-Host ""
Write-Host "This will create:" -ForegroundColor Green
Write-Host "  - Roles: Admin, Attendant, Manager" -ForegroundColor Green
Write-Host "  - Permissions: 50+ permissions" -ForegroundColor Green
Write-Host "  - Users: 3 default users with credentials" -ForegroundColor Green
Write-Host "  - Categories: 6 product categories" -ForegroundColor Green
Write-Host "  - Products: 7 sample products" -ForegroundColor Green
Write-Host "  - Customers: 3 sample customers" -ForegroundColor Green
Write-Host "  - Suppliers: 3 sample suppliers" -ForegroundColor Green
Write-Host "  - CCTV: 4 camera configurations" -ForegroundColor Green