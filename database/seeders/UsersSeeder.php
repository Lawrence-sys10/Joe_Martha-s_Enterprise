<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Check if users already exist
        if (User::where('email', 'admin@jm-ems.com')->exists()) {
            $this->command->info('Users already exist, skipping...');
            return;
        }
        
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