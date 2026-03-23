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

        // Create permissions - check if they exist first
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
            // Check if permission already exists
            if (!Permission::where('name', $permission)->where('guard_name', 'web')->exists()) {
                Permission::create(['name' => $permission, 'guard_name' => 'web']);
            }
        }

        // Create roles - check if they exist first
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $attendantRole = Role::firstOrCreate(['name' => 'Attendant', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);

        // Assign all permissions to Admin
        $allPermissions = Permission::all();
        $adminRole->syncPermissions($allPermissions);

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
        $attendantRole->syncPermissions($attendantPermissions);

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
        $managerRole->syncPermissions($managerPermissions);

        $this->command->info('Roles and permissions created successfully!');
    }
}