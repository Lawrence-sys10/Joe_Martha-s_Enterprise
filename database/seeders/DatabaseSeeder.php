<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Activitylog\Facades\CauserResolver;
use Spatie\Activitylog\ActivityLogger;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Disable activity logging for all seeders
        app(ActivityLogger::class)->disableLogging();
        
        $this->call([
            RolesAndPermissionsSeeder::class,
            UsersSeeder::class,
            CategoriesAndProductsSeeder::class,
            CustomersAndSuppliersSeeder::class,
            CCTVSeeder::class,
        ]);
        
        // Re-enable activity logging
        app(ActivityLogger::class)->enableLogging();
        
        $this->command->info('All seeders completed successfully!');
    }
}