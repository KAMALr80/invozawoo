<?php

namespace Database\Seeders;

use App\Models\CourierPartner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourierPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        CourierPartner::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $couriers = [
            [
                'name' => 'Delhivery',
                'code' => 'DELHIVERY',
                'contact_person' => 'Delhivery Support',
                'contact_email' => 'support@delhivery.com',
                'contact_phone' => '1800-123-4567',
                'address' => 'Delhivery Office, Gurugram, Haryana',
                'api_url' => 'https://track.delhivery.com',
                'api_key' => null,
                'api_secret' => null,
                'api_config' => json_encode(['mode' => 'production']),

                // Service Areas
                'serviceable_pincodes' => json_encode(['110001', '110002', '110003', '400001', '400002', '700001']),
                'serviceable_cities' => json_encode(['Delhi', 'Mumbai', 'Kolkata', 'Chennai', 'Bangalore', 'Hyderabad']),
                'delivery_days' => json_encode([1, 2, 3, 4, 5, 6]), // Mon-Sat
                'cutoff_time' => '18:00:00',
                'holidays' => json_encode(['2024-01-26', '2024-08-15', '2024-10-02']),

                // Pricing
                'rate_card' => json_encode([
                    '0.5' => 50,
                    '1' => 70,
                    '2' => 90,
                    '5' => 120,
                    '10' => 180,
                    '20' => 250
                ]),
                'weight_slabs' => json_encode([
                    ['max_weight' => 0.5, 'rate' => 50],
                    ['max_weight' => 1, 'rate' => 70],
                    ['max_weight' => 2, 'rate' => 90],
                    ['max_weight' => 5, 'rate' => 120],
                    ['max_weight' => 10, 'rate' => 180],
                    ['max_weight' => 20, 'rate' => 250]
                ]),
                'cod_charges' => json_encode([
                    'upto_5000' => 30,
                    'upto_10000' => 50,
                    'upto_25000' => 100,
                    'above_25000' => 0.5,
                    'slabs' => [
                        ['max_amount' => 5000, 'charge' => 30],
                        ['max_amount' => 10000, 'charge' => 50],
                        ['max_amount' => 25000, 'charge' => 100]
                    ]
                ]),

                'volumetric_factor' => 5000,
                'supported_services' => json_encode(['standard', 'express']),
                'tracking_url' => 'https://www.delhivery.com/track?awb={tracking_number}',
                'label_format' => 'pdf',
                'label_size' => 'a4',
                'integration_type' => 'api',
                'logo' => 'couriers/delhivery.png',
                'description' => 'India\'s largest logistics company',
                'is_active' => true,
                'priority' => 1,
            ],
            [
                'name' => 'BlueDart',
                'code' => 'BLUEDART',
                'contact_person' => 'BlueDart Support',
                'contact_email' => 'support@bluedart.com',
                'contact_phone' => '1800-123-7890',
                'address' => 'BlueDart House, Mumbai, Maharashtra',
                'api_url' => 'https://api.bluedart.com',
                'api_key' => null,
                'api_secret' => null,
                'api_config' => json_encode(['mode' => 'production']),

                'serviceable_pincodes' => json_encode(['110001', '400001', '700001', '600001']),
                'serviceable_cities' => json_encode(['Delhi', 'Mumbai', 'Kolkata', 'Chennai', 'Bangalore', 'Pune']),
                'delivery_days' => json_encode([1, 2, 3, 4, 5, 6]),
                'cutoff_time' => '17:00:00',
                'holidays' => json_encode(['2024-01-26', '2024-08-15']),

                'rate_card' => json_encode([
                    '0.5' => 60,
                    '1' => 85,
                    '2' => 110,
                    '5' => 150,
                    '10' => 220,
                    '20' => 300
                ]),
                'weight_slabs' => json_encode([
                    ['max_weight' => 0.5, 'rate' => 60],
                    ['max_weight' => 1, 'rate' => 85],
                    ['max_weight' => 2, 'rate' => 110],
                    ['max_weight' => 5, 'rate' => 150],
                    ['max_weight' => 10, 'rate' => 220],
                    ['max_weight' => 20, 'rate' => 300]
                ]),
                'cod_charges' => json_encode([
                    'upto_5000' => 35,
                    'upto_10000' => 60,
                    'upto_25000' => 120,
                    'above_25000' => 0.6
                ]),

                'volumetric_factor' => 5000,
                'supported_services' => json_encode(['standard', 'express', 'overnight']),
                'tracking_url' => 'https://www.bluedart.com/track?awb={tracking_number}',
                'label_format' => 'pdf',
                'label_size' => 'a4',
                'integration_type' => 'api',
                'logo' => 'couriers/bluedart.png',
                'description' => 'Premium courier service',
                'is_active' => true,
                'priority' => 2,
            ],
            [
                'name' => 'DTDC',
                'code' => 'DTDC',
                'contact_person' => 'DTDC Support',
                'contact_email' => 'support@dtdc.com',
                'contact_phone' => '1800-123-4321',
                'address' => 'DTDC House, Bangalore, Karnataka',
                'api_url' => 'https://api.dtdc.com',
                'api_key' => null,
                'api_secret' => null,
                'api_config' => json_encode(['mode' => 'production']),

                'serviceable_pincodes' => json_encode(['560001', '560002', '560003']),
                'serviceable_cities' => json_encode(['Bangalore', 'Mysore', 'Hubli', 'Mangalore']),
                'delivery_days' => json_encode([1, 2, 3, 4, 5, 6]),
                'cutoff_time' => '16:30:00',

                'rate_card' => json_encode([
                    '0.5' => 45,
                    '1' => 65,
                    '2' => 85,
                    '5' => 110,
                    '10' => 160,
                    '20' => 220
                ]),
                'weight_slabs' => json_encode([
                    ['max_weight' => 0.5, 'rate' => 45],
                    ['max_weight' => 1, 'rate' => 65],
                    ['max_weight' => 2, 'rate' => 85],
                    ['max_weight' => 5, 'rate' => 110],
                    ['max_weight' => 10, 'rate' => 160],
                    ['max_weight' => 20, 'rate' => 220]
                ]),
                'cod_charges' => json_encode([
                    'upto_5000' => 25,
                    'upto_10000' => 45,
                    'upto_25000' => 90,
                    'above_25000' => 0.4
                ]),

                'volumetric_factor' => 6000,
                'supported_services' => json_encode(['standard', 'express']),
                'tracking_url' => 'https://www.dtdc.in/track.asp?awb={tracking_number}',
                'label_format' => 'pdf',
                'label_size' => 'a4',
                'integration_type' => 'api',
                'logo' => 'couriers/dtdc.png',
                'description' => 'Affordable courier solutions',
                'is_active' => true,
                'priority' => 3,
            ],
            [
                'name' => 'FedEx',
                'code' => 'FEDEX',
                'contact_person' => 'FedEx Support',
                'contact_email' => 'support@fedex.com',
                'contact_phone' => '1800-123-9876',
                'address' => 'FedEx Office, Mumbai, Maharashtra',
                'api_url' => 'https://api.fedex.com',
                'api_key' => null,
                'api_secret' => null,
                'api_config' => json_encode(['mode' => 'production']),

                'serviceable_pincodes' => json_encode(['110001', '400001', '700001', '600001']),
                'serviceable_cities' => json_encode(['Delhi', 'Mumbai', 'Kolkata', 'Chennai', 'Bangalore', 'Hyderabad']),
                'delivery_days' => json_encode([1, 2, 3, 4, 5, 6]),
                'cutoff_time' => '17:30:00',

                'rate_card' => json_encode([
                    '0.5' => 80,
                    '1' => 120,
                    '2' => 160,
                    '5' => 220,
                    '10' => 300,
                    '20' => 400
                ]),
                'weight_slabs' => json_encode([
                    ['max_weight' => 0.5, 'rate' => 80],
                    ['max_weight' => 1, 'rate' => 120],
                    ['max_weight' => 2, 'rate' => 160],
                    ['max_weight' => 5, 'rate' => 220],
                    ['max_weight' => 10, 'rate' => 300],
                    ['max_weight' => 20, 'rate' => 400]
                ]),
                'cod_charges' => json_encode([
                    'percentage' => 1.5,
                    'minimum' => 40,
                    'default' => 50
                ]),

                'volumetric_factor' => 5000,
                'supported_services' => json_encode(['standard', 'express', 'overnight', 'international']),
                'tracking_url' => 'https://www.fedex.com/apps/fedextrack/?action=track&tracknumbers={tracking_number}',
                'label_format' => 'pdf',
                'label_size' => 'a4',
                'integration_type' => 'api',
                'logo' => 'couriers/fedex.png',
                'description' => 'Global courier leader',
                'is_active' => true,
                'priority' => 4,
            ],
            [
                'name' => 'Ekart',
                'code' => 'EKART',
                'contact_person' => 'Ekart Support',
                'contact_email' => 'support@ekart.com',
                'contact_phone' => '1800-123-1111',
                'address' => 'Ekart Logistics, Bangalore, Karnataka',
                'api_url' => 'https://api.ekartlogistics.com',
                'api_key' => null,
                'api_secret' => null,
                'api_config' => json_encode(['mode' => 'production']),

                'serviceable_pincodes' => json_encode(['560001', '560002', '560003']),
                'serviceable_cities' => json_encode(['Bangalore', 'Mysore', 'Hubli']),
                'delivery_days' => json_encode([1, 2, 3, 4, 5, 6]),
                'cutoff_time' => '16:00:00',

                'rate_card' => json_encode([
                    '0.5' => 40,
                    '1' => 55,
                    '2' => 75,
                    '5' => 100,
                    '10' => 140,
                    '20' => 200
                ]),
                'weight_slabs' => json_encode([
                    ['max_weight' => 0.5, 'rate' => 40],
                    ['max_weight' => 1, 'rate' => 55],
                    ['max_weight' => 2, 'rate' => 75],
                    ['max_weight' => 5, 'rate' => 100],
                    ['max_weight' => 10, 'rate' => 140],
                    ['max_weight' => 20, 'rate' => 200]
                ]),
                'cod_charges' => json_encode([
                    'upto_5000' => 20,
                    'upto_10000' => 35,
                    'upto_25000' => 70,
                    'above_25000' => 0.3
                ]),

                'volumetric_factor' => 5000,
                'supported_services' => json_encode(['standard']),
                'tracking_url' => 'https://ekart.com/track?awb={tracking_number}',
                'label_format' => 'pdf',
                'label_size' => 'a4',
                'integration_type' => 'api',
                'logo' => 'couriers/ekart.png',
                'description' => 'Flipkart\'s logistics arm',
                'is_active' => true,
                'priority' => 5,
            ],
            [
                'name' => 'XpressBees',
                'code' => 'XPRESSBEES',
                'contact_person' => 'XpressBees Support',
                'contact_email' => 'support@xpressbees.com',
                'contact_phone' => '1800-123-2222',
                'address' => 'XpressBees Office, Pune, Maharashtra',
                'api_url' => 'https://api.xpressbees.com',
                'api_key' => null,
                'api_secret' => null,
                'api_config' => json_encode(['mode' => 'production']),

                'serviceable_pincodes' => json_encode(['411001', '411002', '411003']),
                'serviceable_cities' => json_encode(['Pune', 'Mumbai', 'Nashik']),
                'delivery_days' => json_encode([1, 2, 3, 4, 5, 6]),
                'cutoff_time' => '17:00:00',

                'rate_card' => json_encode([
                    '0.5' => 42,
                    '1' => 58,
                    '2' => 80,
                    '5' => 105,
                    '10' => 150,
                    '20' => 210
                ]),
                'weight_slabs' => json_encode([
                    ['max_weight' => 0.5, 'rate' => 42],
                    ['max_weight' => 1, 'rate' => 58],
                    ['max_weight' => 2, 'rate' => 80],
                    ['max_weight' => 5, 'rate' => 105],
                    ['max_weight' => 10, 'rate' => 150],
                    ['max_weight' => 20, 'rate' => 210]
                ]),
                'cod_charges' => json_encode([
                    'upto_5000' => 22,
                    'upto_10000' => 38,
                    'upto_25000' => 75,
                    'above_25000' => 0.35
                ]),

                'volumetric_factor' => 5000,
                'supported_services' => json_encode(['standard', 'express']),
                'tracking_url' => 'https://www.xpressbees.com/track?awb={tracking_number}',
                'label_format' => 'pdf',
                'label_size' => 'a4',
                'integration_type' => 'api',
                'logo' => 'couriers/xpressbees.png',
                'description' => 'Fast and reliable delivery',
                'is_active' => true,
                'priority' => 6,
            ],
            [
                'name' => 'Shadowfax',
                'code' => 'SHADOWFAX',
                'contact_person' => 'Shadowfax Support',
                'contact_email' => 'support@shadowfax.in',
                'contact_phone' => '1800-123-3333',
                'address' => 'Shadowfax Office, Bangalore, Karnataka',
                'api_url' => 'https://api.shadowfax.in',
                'api_key' => null,
                'api_secret' => null,
                'api_config' => json_encode(['mode' => 'production']),

                'serviceable_pincodes' => json_encode(['560001', '560002']),
                'serviceable_cities' => json_encode(['Bangalore', 'Mumbai', 'Delhi']),
                'delivery_days' => json_encode([1, 2, 3, 4, 5, 6, 7]), // All days
                'cutoff_time' => '19:00:00',

                'rate_card' => json_encode([
                    '0.5' => 38,
                    '1' => 52,
                    '2' => 72,
                    '5' => 95,
                    '10' => 135,
                    '20' => 190
                ]),
                'weight_slabs' => json_encode([
                    ['max_weight' => 0.5, 'rate' => 38],
                    ['max_weight' => 1, 'rate' => 52],
                    ['max_weight' => 2, 'rate' => 72],
                    ['max_weight' => 5, 'rate' => 95],
                    ['max_weight' => 10, 'rate' => 135],
                    ['max_weight' => 20, 'rate' => 190]
                ]),
                'cod_charges' => json_encode([
                    'upto_5000' => 18,
                    'upto_10000' => 32,
                    'upto_25000' => 65,
                    'above_25000' => 0.28
                ]),

                'volumetric_factor' => 5000,
                'supported_services' => json_encode(['standard', 'express']),
                'tracking_url' => 'https://shadowfax.in/track?awb={tracking_number}',
                'label_format' => 'pdf',
                'label_size' => 'a4',
                'integration_type' => 'api',
                'logo' => 'couriers/shadowfax.png',
                'description' => 'Hyperlocal delivery expert',
                'is_active' => true,
                'priority' => 7,
            ],
        ];

        foreach ($couriers as $courier) {
            CourierPartner::create($courier);
        }

        $this->command->info('✅ ' . count($couriers) . ' courier partners seeded successfully!');
    }
}
