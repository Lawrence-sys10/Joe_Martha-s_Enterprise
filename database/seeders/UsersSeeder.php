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
        if (User::where('email', 'pprhlawrence@gmail.com')->exists()) {
            $this->command->info('Users already exist, skipping...');
            return;
        }
        
        // Create admin user
        User::firstOrCreate(
            ['email' => 'pprhlawrence@gmail.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'phone' => '0593001501',
                'address' => 'Sunyani Technical University',
                'is_active' => true,
            ]
        )->syncRoles('Admin');

        // Create attendant user
        User::firstOrcreate(
            ['email' => 'attendant@jm-ems.com'],
            [
                'name' => 'Shop Attendant',
                'password' => Hash::make('password'),
                'phone' => '0593001502',
                'address' => 'Shop Location',
                'is_active' => true,
            ]
        )->syncRoles('Attendant');
        

        // Create manager user
        //$manager = User::create([
           // 'name' => 'Shop Manager',
            //'email' => 'manager@jm-ems.com',
           // 'password' => Hash::make('password'),
            //'phone' => '0593001503',
           // 'address' => 'Sunyani Technical University',
            //'is_active' => true,
        //]);
        //$manager->assignRole('Manager');

        $this->command->info('Users created successfully!');
        $this->command->info('Admin: pprhlawrence@gmail.com / password');
        $this->command->info('Attendant: attendant@jm-ems.com / password');
        //$this->command->info('Manager: manager@jm-ems.com / password');
    }
}