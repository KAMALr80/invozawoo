<?php

namespace Database\Seeders;

use App\Models\CustomerAddress;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CustomerAddress::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $customers = Customer::all();

        if ($customers->isEmpty()) {
            $this->command->warn('⚠️ No customers found. Skipping CustomerAddressSeeder.');
            return;
        }

        $addresses = [
            // Bangalore addresses
            [
                'customer_pattern' => 'Rajesh',
                'addresses' => [
                    [
                        'type' => 'home',
                        'name' => 'Home',
                        'receiver' => 'Rajesh Kumar',
                        'phone' => '9876543210',
                        'line1' => '123, 4th Main, Indiranagar',
                        'landmark' => 'Near 100ft Road',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'pincode' => '560038',
                        'lat' => 12.9784,
                        'lng' => 77.6408,
                    ],
                    [
                        'type' => 'office',
                        'name' => 'Office',
                        'receiver' => 'Rajesh Kumar',
                        'phone' => '9876543210',
                        'line1' => '456, Embassy Manyata',
                        'line2' => 'Outer Ring Road',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'pincode' => '560045',
                        'lat' => 13.0358,
                        'lng' => 77.6621,
                    ],
                ],
            ],
            // Mumbai addresses
            [
                'customer_pattern' => 'Priya',
                'addresses' => [
                    [
                        'type' => 'home',
                        'name' => 'Home',
                        'receiver' => 'Priya Sharma',
                        'phone' => '9876543212',
                        'line1' => '789, Lokhandwala Complex',
                        'line2' => 'Andheri West',
                        'landmark' => 'Near Infinity Mall',
                        'city' => 'Mumbai',
                        'state' => 'Maharashtra',
                        'pincode' => '400053',
                        'lat' => 19.1364,
                        'lng' => 72.8290,
                    ],
                    [
                        'type' => 'office',
                        'name' => 'Office',
                        'receiver' => 'Priya Sharma',
                        'phone' => '9876543212',
                        'line1' => '321, BKC Complex',
                        'line2' => 'Bandra East',
                        'city' => 'Mumbai',
                        'state' => 'Maharashtra',
                        'pincode' => '400051',
                        'lat' => 19.0760,
                        'lng' => 72.8777,
                    ],
                ],
            ],
            // Ahmedabad addresses
            [
                'customer_pattern' => 'Amit',
                'addresses' => [
                    [
                        'type' => 'home',
                        'name' => 'Home',
                        'receiver' => 'Amit Patel',
                        'phone' => '9876543214',
                        'line1' => '234, Satellite Road',
                        'landmark' => 'Near Alpha One Mall',
                        'city' => 'Ahmedabad',
                        'state' => 'Gujarat',
                        'pincode' => '380015',
                        'lat' => 23.0225,
                        'lng' => 72.5714,
                    ],
                ],
            ],
            // Hyderabad addresses
            [
                'customer_pattern' => 'Sunita',
                'addresses' => [
                    [
                        'type' => 'home',
                        'name' => 'Home',
                        'receiver' => 'Sunita Reddy',
                        'phone' => '9876543216',
                        'line1' => '567, Jubilee Hills',
                        'landmark' => 'Near KBR Park',
                        'city' => 'Hyderabad',
                        'state' => 'Telangana',
                        'pincode' => '500033',
                        'lat' => 17.4319,
                        'lng' => 78.4098,
                    ],
                    [
                        'type' => 'office',
                        'name' => 'Office',
                        'receiver' => 'Sunita Reddy',
                        'phone' => '9876543216',
                        'line1' => '890, Hitech City',
                        'city' => 'Hyderabad',
                        'state' => 'Telangana',
                        'pincode' => '500081',
                        'lat' => 17.4483,
                        'lng' => 78.3915,
                    ],
                ],
            ],
            // Delhi addresses
            [
                'customer_pattern' => 'Vikram',
                'addresses' => [
                    [
                        'type' => 'home',
                        'name' => 'Home',
                        'receiver' => 'Vikram Singh',
                        'phone' => '9876543218',
                        'line1' => '432, Connaught Place',
                        'landmark' => 'Near Rajiv Chowk',
                        'city' => 'Delhi',
                        'state' => 'Delhi',
                        'pincode' => '110001',
                        'lat' => 28.6329,
                        'lng' => 77.2199,
                    ],
                    [
                        'type' => 'office',
                        'name' => 'Office',
                        'receiver' => 'Vikram Singh',
                        'phone' => '9876543218',
                        'line1' => '765, Nehru Place',
                        'city' => 'Delhi',
                        'state' => 'Delhi',
                        'pincode' => '110019',
                        'lat' => 28.5672,
                        'lng' => 77.2126,
                    ],
                ],
            ],
            // Pune addresses
            [
                'customer_pattern' => 'Kavita',
                'addresses' => [
                    [
                        'type' => 'home',
                        'name' => 'Home',
                        'receiver' => 'Kavita Joshi',
                        'phone' => '9876543220',
                        'line1' => '876, Koregaon Park',
                        'landmark' => 'Near Osho Ashram',
                        'city' => 'Pune',
                        'state' => 'Maharashtra',
                        'pincode' => '411001',
                        'lat' => 18.5362,
                        'lng' => 73.8897,
                    ],
                ],
            ],
            // Kolkata addresses
            [
                'customer_pattern' => 'Rahul',
                'addresses' => [
                    [
                        'type' => 'home',
                        'name' => 'Home',
                        'receiver' => 'Rahul Verma',
                        'phone' => '9876543222',
                        'line1' => '543, Salt Lake City',
                        'landmark' => 'Near City Center',
                        'city' => 'Kolkata',
                        'state' => 'West Bengal',
                        'pincode' => '700091',
                        'lat' => 22.5802,
                        'lng' => 88.4297,
                    ],
                ],
            ],
            // Chennai addresses
            [
                'customer_pattern' => 'Anjali',
                'addresses' => [
                    [
                        'type' => 'home',
                        'name' => 'Home',
                        'receiver' => 'Anjali Desai',
                        'phone' => '9876543224',
                        'line1' => '987, Alwarpet',
                        'landmark' => 'Near RK Mutt Road',
                        'city' => 'Chennai',
                        'state' => 'Tamil Nadu',
                        'pincode' => '600018',
                        'lat' => 13.0358,
                        'lng' => 80.2425,
                    ],
                    [
                        'type' => 'office',
                        'name' => 'Office',
                        'receiver' => 'Anjali Desai',
                        'phone' => '9876543224',
                        'line1' => '654, T Nagar',
                        'city' => 'Chennai',
                        'state' => 'Tamil Nadu',
                        'pincode' => '600017',
                        'lat' => 13.0418,
                        'lng' => 80.2348,
                    ],
                ],
            ],
            // Kochi addresses
            [
                'customer_pattern' => 'Suresh',
                'addresses' => [
                    [
                        'type' => 'home',
                        'name' => 'Home',
                        'receiver' => 'Suresh Nair',
                        'phone' => '9876543226',
                        'line1' => '321, Panampilly Nagar',
                        'landmark' => 'Near Avenue Centre',
                        'city' => 'Kochi',
                        'state' => 'Kerala',
                        'pincode' => '682036',
                        'lat' => 9.9654,
                        'lng' => 76.2929,
                    ],
                ],
            ],
            // Coimbatore addresses
            [
                'customer_pattern' => 'Meera',
                'addresses' => [
                    [
                        'type' => 'home',
                        'name' => 'Home',
                        'receiver' => 'Meera Iyer',
                        'phone' => '9876543228',
                        'line1' => '159, Race Course Road',
                        'landmark' => 'Near Ganga Hospital',
                        'city' => 'Coimbatore',
                        'state' => 'Tamil Nadu',
                        'pincode' => '641018',
                        'lat' => 11.0045,
                        'lng' => 76.9610,
                    ],
                ],
            ],
        ];

        foreach ($addresses as $addressGroup) {
            // Find matching customers
            $matchingCustomers = $customers->filter(function($customer) use ($addressGroup) {
                return str_contains($customer->name, $addressGroup['customer_pattern']);
            });

            if ($matchingCustomers->isEmpty()) {
                // Take random customer if pattern not found
                $customer = $customers->random();
            } else {
                $customer = $matchingCustomers->first();
            }

            foreach ($addressGroup['addresses'] as $index => $addrData) {
                $isDefault = ($index === 0); // First address is default

                $address = CustomerAddress::create([
                    'customer_id' => $customer->id,
                    'address_type' => $addrData['type'],
                    'name' => $addrData['name'],
                    'receiver_name' => $addrData['receiver'],
                    'receiver_phone' => $addrData['phone'],
                    'alternate_phone' => rand(0, 1) ? '9876543000' : null,
                    'address_line1' => $addrData['line1'],
                    'address_line2' => $addrData['line2'] ?? null,
                    'landmark' => $addrData['landmark'] ?? null,
                    'city' => $addrData['city'],
                    'state' => $addrData['state'],
                    'pincode' => $addrData['pincode'],
                    'country' => 'India',
                    'latitude' => $addrData['lat'],
                    'longitude' => $addrData['lng'],
                    'place_id' => 'place_' . md5($addrData['line1']),
                    'is_default' => $isDefault,
                    'delivery_instructions' => rand(0, 1) ? 'Leave with neighbour' : null,
                    'is_active' => true,
                ]);

                if ($isDefault) {
                    // Update customer's default address
                    $customer->default_address_id = $address->id;
                    $customer->default_latitude = $addrData['lat'];
                    $customer->default_longitude = $addrData['lng'];
                    $customer->save();
                }

                $this->command->info("✅ Address added for {$customer->name}: {$addrData['type']}");
            }
        }

        $totalAddresses = CustomerAddress::count();
        $this->command->info("✅ {$totalAddresses} customer addresses seeded successfully!");
    }
}
