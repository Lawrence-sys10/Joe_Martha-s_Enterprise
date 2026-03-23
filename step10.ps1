# Step10-FinalConfiguration-Fixed.ps1
# Fixed version with proper error handling

Write-Host "Step 10: Final Configuration and Testing..." -ForegroundColor Green

# Create necessary directories
New-Item -ItemType Directory -Force -Path "config" | Out-Null
New-Item -ItemType Directory -Force -Path "app\Console\Commands" | Out-Null

# Update app.php configuration
@'
<?php

return [
    'name' => env('APP_NAME', 'JM-EMS'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL', null),
    'timezone' => 'Africa/Accra',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'maintenance' => [
        'driver' => 'file',
    ],
];
'@ | Out-File -FilePath "config/app.php" -Encoding UTF8 -NoNewline

# Update database configuration
@'
<?php

use Illuminate\Support\Str;

return [
    'default' => env('DB_CONNECTION', 'mysql'),
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'joan_mat_enterprise'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
    ],
    'migrations' => 'migrations',
    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],
        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
    ],
];
'@ | Out-File -FilePath "config/database.php" -Encoding UTF8 -NoNewline

# Create permission configuration
@'
<?php

return [
    'models' => [
        'permission' => Spatie\Permission\Models\Permission::class,
        'role' => Spatie\Permission\Models\Role::class,
    ],
    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],
    'column_names' => [
        'role_pivot_key' => null,
        'model_morph_key' => 'model_id',
        'team_foreign_key' => 'team_id',
    ],
    'register_permission_check_method' => true,
    'teams' => false,
    'display_v_name' => false,
    'guard_name' => 'web',
];
'@ | Out-File -FilePath "config/permission.php" -Encoding UTF8 -NoNewline

# Create .env.example file
@'
APP_NAME="JM-EMS"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=joan_mat_enterprise
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@jm-ems.com"
MAIL_FROM_NAME="${APP_NAME}"
'@ | Out-File -FilePath ".env.example" -Encoding UTF8 -NoNewline

# Create test command to verify installation
@'
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VerifyInstallation extends Command
{
    protected $signature = 'jm-ems:verify';
    protected $description = 'Verify JM-EMS installation and configuration';

    public function handle()
    {
        $this->info('=================================');
        $this->info('JM-EMS Installation Verification');
        $this->info('=================================');
        $this->newLine();

        // Check database connection
        $this->info('1. Checking Database Connection...');
        try {
            DB::connection()->getPdo();
            $this->info('   ✓ Database connection successful');
        } catch (\Exception $e) {
            $this->error('   ✗ Database connection failed: ' . $e->getMessage());
            return 1;
        }

        // Check tables
        $this->info('2. Checking Database Tables...');
        $tables = ['users', 'products', 'sales', 'categories', 'customers', 'suppliers', 'cctvs'];
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                $this->info("   ✓ Table '$table' exists ($count records)");
            } else {
                $this->warn("   ⚠ Table '$table' not found");
            }
        }

        // Check users
        $this->info('3. Checking Users...');
        $users = User::all();
        $this->info("   ✓ Total users: {$users->count()}");
        foreach ($users as $user) {
            $roles = $user->getRoleNames()->toArray();
            $roleText = !empty($roles) ? implode(', ', $roles) : 'No roles assigned';
            $this->line("      - {$user->name} ({$user->email}) - Roles: {$roleText}");
        }

        // Check products
        $this->info('4. Checking Products...');
        $products = Product::all();
        $this->info("   ✓ Total products: {$products->count()}");
        if ($products->count() > 0) {
            $lowStock = $products->filter(function($p) {
                return $p->stock_quantity <= $p->minimum_stock;
            });
            if ($lowStock->count() > 0) {
                $this->warn("   ⚠ Low stock items: {$lowStock->count()}");
                foreach ($lowStock as $product) {
                    $this->line("      - {$product->name}: Stock {$product->stock_quantity} / Min {$product->minimum_stock}");
                }
            }
        }

        // Check sales
        $this->info('5. Checking Sales...');
        $sales = Sale::count();
        $this->info("   ✓ Total sales: {$sales}");
        
        $todaySales = Sale::whereDate('created_at', now())->count();
        $this->info("   ✓ Today's sales: {$todaySales}");

        // Check roles and permissions
        $this->info('6. Checking Roles and Permissions...');
        if (class_exists('Spatie\Permission\Models\Role')) {
            $roles = \Spatie\Permission\Models\Role::all();
            $this->info("   ✓ Roles: " . $roles->pluck('name')->implode(', '));
            
            $permissions = \Spatie\Permission\Models\Permission::count();
            $this->info("   ✓ Permissions: {$permissions}");
        } else {
            $this->warn("   ⚠ Spatie Permission package not loaded");
        }

        // Check CCTV
        $this->info('7. Checking CCTV Configuration...');
        if (class_exists('App\Models\CCTV')) {
            $cameras = \App\Models\CCTV::count();
            $this->info("   ✓ Cameras configured: {$cameras}");
        } else {
            $this->warn("   ⚠ CCTV model not found");
        }

        $this->newLine();
        $this->info('=================================');
        $this->info('Installation Verification Complete!');
        $this->info('=================================');
        $this->newLine();
        
        $this->info('Login Credentials:');
        $this->info('   Admin: admin@jm-ems.com / password');
        $this->info('   Attendant: attendant@jm-ems.com / password');
        $this->info('   Manager: manager@jm-ems.com / password');
        $this->newLine();
        
        $this->info('To start the application, run:');
        $this->info('   php artisan serve');
        $this->info('Then visit: http://localhost:8000/login');
        
        return 0;
    }
}
'@ | Out-File -FilePath "app\Console\Commands\VerifyInstallation.php" -Encoding UTF8 -NoNewline

# Create installation script
@'
Write-Host "======================================" -ForegroundColor Cyan
Write-Host "JM-EMS Installation Script" -ForegroundColor Cyan
Write-Host "Joan-Mat Enterprise Management System" -ForegroundColor Cyan
Write-Host "======================================" -ForegroundColor Cyan
Write-Host ""

# Check PHP version
Write-Host "Checking PHP version..." -ForegroundColor Yellow
try {
    $phpVersion = php -v 2>&1 | Select-String "PHP ([0-9.]+)"
    if ($phpVersion -match "PHP 8\.[0-9.]+") {
        Write-Host "✓ PHP version OK: $($Matches[1])" -ForegroundColor Green
    } else {
        Write-Host "⚠ PHP version may be below 8.0" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠ Could not determine PHP version" -ForegroundColor Yellow
}

# Check Composer
Write-Host "Checking Composer..." -ForegroundColor Yellow
$composer = Get-Command composer -ErrorAction SilentlyContinue
if ($composer) {
    Write-Host "✓ Composer found" -ForegroundColor Green
} else {
    Write-Host "✗ Composer not found. Please install Composer first." -ForegroundColor Red
    exit
}

# Copy .env file
if (!(Test-Path ".env")) {
    Write-Host "Creating .env file..." -ForegroundColor Yellow
    Copy-Item ".env.example" ".env"
}

# Install dependencies
Write-Host "Installing Composer dependencies..." -ForegroundColor Yellow
composer install --no-interaction

# Generate key
Write-Host "Generating application key..." -ForegroundColor Yellow
php artisan key:generate --no-interaction

# Run migrations
Write-Host "Running migrations..." -ForegroundColor Yellow
php artisan migrate:fresh --seed --no-interaction

# Create storage link
Write-Host "Creating storage link..." -ForegroundColor Yellow
php artisan storage:link --no-interaction

# Optimize
Write-Host "Optimizing application..." -ForegroundColor Yellow
php artisan optimize --no-interaction

# Verify installation
Write-Host "Verifying installation..." -ForegroundColor Yellow
php artisan jm-ems:verify --no-interaction

Write-Host ""
Write-Host "======================================" -ForegroundColor Green
Write-Host "Installation Complete!" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green
Write-Host ""
Write-Host "To start the application, run:" -ForegroundColor Yellow
Write-Host "php artisan serve" -ForegroundColor Cyan
Write-Host ""
Write-Host "Login Credentials:" -ForegroundColor Yellow
Write-Host "Admin: admin@jm-ems.com / password" -ForegroundColor Cyan
Write-Host "Attendant: attendant@jm-ems.com / password" -ForegroundColor Cyan
Write-Host "Manager: manager@jm-ems.com / password" -ForegroundColor Cyan
Write-Host ""
Read-Host "Press Enter to exit"
'@ | Out-File -FilePath "install.ps1" -Encoding UTF8 -NoNewline

# Create README file
@'
# Joan-Mat Enterprise Management System (JM-EMS)

## Overview
A comprehensive retail management system for provision shops in Ghana, designed to manage inventory, sales, customers, suppliers, and financial records with integrated CCTV monitoring.

## Features
- **Inventory Management**: Track stock levels, low stock alerts, barcode support
- **Point of Sale (POS)**: Fast checkout with cart system and receipt generation
- **Customer Management**: Track customer credit and purchase history
- **Supplier Management**: Manage supplier information and purchase orders
- **Financial Management**: Track expenses, profit/loss reports
- **CCTV Integration**: Monitor and log camera events
- **Role-Based Access**: Admin, Manager, and Attendant roles with granular permissions
- **Reporting**: Daily/Monthly sales, stock valuation, top products
- **Activity Logging**: Complete audit trail of all actions

## System Requirements
- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- Node.js (for frontend assets)
- Redis (optional, for caching)

## Installation

### Quick Install
Run the installation script:
```bash
.\install.ps1
'@