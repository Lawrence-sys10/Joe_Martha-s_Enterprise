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