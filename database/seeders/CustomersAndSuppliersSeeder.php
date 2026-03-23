<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Supplier;

class CustomersAndSuppliersSeeder extends Seeder
{
    public function run(): void
    {
        // Check if customers already exist
        if (Customer::count() > 0) {
            $this->command->info('Customers and suppliers already exist, skipping...');
            return;
        }
        
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