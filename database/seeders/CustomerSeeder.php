<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Rajesh Kumar',
                'mobile' => '9876543210',
                'email' => 'rajesh.kumar@example.com',
                'address' => '123, MG Road, Indiranagar',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode' => '560038',
                'gst_no' => '29ABCDE1234F1Z5',
                'open_balance' => 0,
                'wallet_balance' => 500,
            ],
            [
                'name' => 'Priya Sharma',
                'mobile' => '9876543212',
                'email' => 'priya.sharma@example.com',
                'address' => '456, Lokhandwala Complex',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400053',
                'gst_no' => '27FGHIJ5678K2L6',
                'open_balance' => 1000,
                'wallet_balance' => 200,
            ],
            [
                'name' => 'Amit Patel',
                'mobile' => '9876543214',
                'email' => 'amit.patel@example.com',
                'address' => '789, Satellite Road',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'pincode' => '380015',
                'gst_no' => '24KLMNO9012P3M7',
                'open_balance' => 0,
                'wallet_balance' => 1000,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        $this->command->info('✅ Customers seeded successfully!');
    }
}
