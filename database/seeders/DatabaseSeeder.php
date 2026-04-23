<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Basic seeders - Create these first
            UserSeeder::class,
            EmployeeSeeder::class,
            CustomerSeeder::class,
            ProductSeeder::class,

            // Logistics seeders
            CourierPartnerSeeder::class,
            DeliveryAgentSeeder::class,

            // Address seeders (depends on customers)
            CustomerAddressSeeder::class,

            // Route seeders (depends on agents)
            SavedRouteSeeder::class,
            AgentPerformanceLogSeeder::class,
        ]);
    }
}
