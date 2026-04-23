<?php
// database/seeders/SimpleAgentSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DeliveryAgent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SimpleAgentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting Agent Seeder...');

        // Delivery agents data with unique phone numbers
        $agents = [
            [
                'name' => 'Rahul Sharma',
                'email' => 'rahul.sharma@delivery.com',
                'mobile' => '9876543210',
                'password' => 'password123',
                'role' => 'delivery_agent',
                'status' => 'active',
                'agent_code' => 'AG001',
                'vehicle_type' => 'Motorcycle',
                'vehicle_number' => 'GJ-01-AB-1234',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'pincode' => '380001',
                'current_latitude' => 22.524768,
                'current_longitude' => 72.955568,
                'rating' => 4.8,
                'total_deliveries' => 245,
                'successful_deliveries' => 238,
                'joining_date' => Carbon::now()->format('Y-m-d')
            ],
            [
                'name' => 'Amit Patel',
                'email' => 'amit.patel@delivery.com',
                'mobile' => '9876543211',
                'password' => 'password123',
                'role' => 'delivery_agent',
                'status' => 'active',
                'agent_code' => 'AG002',
                'vehicle_type' => 'Motorcycle',
                'vehicle_number' => 'GJ-02-CD-5678',
                'city' => 'Anand',
                'state' => 'Gujarat',
                'pincode' => '388001',
                'current_latitude' => 22.624768,
                'current_longitude' => 72.855568,
                'rating' => 4.9,
                'total_deliveries' => 312,
                'successful_deliveries' => 305,
                'joining_date' => Carbon::now()->format('Y-m-d')
            ],
            [
                'name' => 'Priya Singh',
                'email' => 'priya.singh@delivery.com',
                'mobile' => '9876543212',
                'password' => 'password123',
                'role' => 'delivery_agent',
                'status' => 'active',
                'agent_code' => 'AG003',
                'vehicle_type' => 'Scooter',
                'vehicle_number' => 'GJ-06-EF-9012',
                'city' => 'Vadodara',
                'state' => 'Gujarat',
                'pincode' => '390001',
                'current_latitude' => 22.424768,
                'current_longitude' => 73.055568,
                'rating' => 4.7,
                'total_deliveries' => 178,
                'successful_deliveries' => 170,
                'joining_date' => Carbon::now()->format('Y-m-d')
            ],
            [
                'name' => 'Vikram Mehta',
                'email' => 'vikram.mehta@delivery.com',
                'mobile' => '9876543213',
                'password' => 'password123',
                'role' => 'delivery_agent',
                'status' => 'active',
                'agent_code' => 'AG004',
                'vehicle_type' => 'Bicycle',
                'vehicle_number' => 'GJ-18-GH-3456',
                'city' => 'Gandhinagar',
                'state' => 'Gujarat',
                'pincode' => '382010',
                'current_latitude' => 22.724768,
                'current_longitude' => 72.755568,
                'rating' => 4.6,
                'total_deliveries' => 98,
                'successful_deliveries' => 92,
                'joining_date' => Carbon::now()->format('Y-m-d')
            ],
            [
                'name' => 'Neha Gupta',
                'email' => 'neha.gupta@delivery.com',
                'mobile' => '9876543214',
                'password' => 'password123',
                'role' => 'delivery_agent',
                'status' => 'active',
                'agent_code' => 'AG005',
                'vehicle_type' => 'Motorcycle',
                'vehicle_number' => 'GJ-05-IJ-7890',
                'city' => 'Surat',
                'state' => 'Gujarat',
                'pincode' => '395001',
                'current_latitude' => 22.824768,
                'current_longitude' => 72.655568,
                'rating' => 4.9,
                'total_deliveries' => 456,
                'successful_deliveries' => 450,
                'joining_date' => Carbon::now()->format('Y-m-d')
            ],
            [
                'name' => 'Rajesh Kumar',
                'email' => 'rajesh.kumar@example.com',
                'mobile' => '9876543215',
                'password' => 'password123',
                'role' => 'delivery_agent',
                'status' => 'active',
                'agent_code' => 'AG006',
                'vehicle_type' => 'Motorcycle',
                'vehicle_number' => 'GJ-01-KL-1234',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'pincode' => '380002',
                'current_latitude' => 22.524768,
                'current_longitude' => 72.955568,
                'rating' => 4.5,
                'total_deliveries' => 67,
                'successful_deliveries' => 60,
                'joining_date' => Carbon::now()->format('Y-m-d')
            ]
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($agents as $agentData) {
            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $agentData['email']],
                [
                    'name' => $agentData['name'],
                    'mobile' => $agentData['mobile'],
                    'password' => Hash::make($agentData['password']),
                    'role' => $agentData['role'],
                    'status' => $agentData['status'],
                    'current_latitude' => $agentData['current_latitude'],
                    'current_longitude' => $agentData['current_longitude'],
                    'email_verified_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            );

            // Check if delivery agent already exists by email or agent_code
            $existingAgent = DeliveryAgent::where('email', $agentData['email'])
                ->orWhere('agent_code', $agentData['agent_code'])
                ->first();

            $deliveryAgentData = [
                'user_id' => $user->id,
                'name' => $agentData['name'],
                'phone' => $agentData['mobile'],
                'email' => $agentData['email'],
                'agent_code' => $agentData['agent_code'],
                'vehicle_type' => $agentData['vehicle_type'],
                'vehicle_number' => $agentData['vehicle_number'],
                'city' => $agentData['city'],
                'state' => $agentData['state'],
                'pincode' => $agentData['pincode'],
                'current_latitude' => $agentData['current_latitude'],
                'current_longitude' => $agentData['current_longitude'],
                'rating' => $agentData['rating'],
                'total_deliveries' => $agentData['total_deliveries'],
                'successful_deliveries' => $agentData['successful_deliveries'],
                'status' => 'available',
                'is_active' => true,
                'is_online' => true,
                'joining_date' => $agentData['joining_date'],
                'last_active_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now()
            ];

            if ($existingAgent) {
                // Update existing agent
                $existingAgent->update($deliveryAgentData);
                $updatedCount++;
                $this->command->info("🔄 Updated: {$agentData['name']} ({$agentData['agent_code']})");
            } else {
                // Create new agent
                DeliveryAgent::create($deliveryAgentData);
                $createdCount++;
                $this->command->info("✅ Created: {$agentData['name']} ({$agentData['agent_code']})");
            }
        }

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info("✅ Agent Seeder Completed!");
        $this->command->info("   Created: {$createdCount} new agents");
        $this->command->info("   Updated: {$updatedCount} existing agents");
        $this->command->info("   Total: " . count($agents) . " agents");
        $this->command->info('========================================');
    }
}
