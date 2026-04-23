<?php
// database/seeders/AgentSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DeliveryAgent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Agent Seeder...');

        // Define delivery agents data
        $agents = [
            [
                'name' => 'Rahul Sharma',
                'email' => 'rahul.sharma@delivery.com',
                'mobile' => '9876543210',
                'password' => 'password123',
                'role' => 'delivery_agent',
                'status' => 'active',
                'rating' => 4.8,
                'total_deliveries' => 245,
                'successful_deliveries' => 238,
                'current_latitude' => 22.524768,
                'current_longitude' => 72.955568,
                'current_location' => 'Ahmedabad, Gujarat',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'pincode' => '380001',
                'vehicle_type' => 'Motorcycle',
                'vehicle_number' => 'GJ-01-AB-1234',
                'agent_code' => 'AG001',
                'is_online' => true,
                'is_active' => true
            ],
            [
                'name' => 'Amit Patel',
                'email' => 'amit.patel@delivery.com',
                'mobile' => '9876543211',
                'password' => 'password123',
                'role' => 'delivery_agent',
                'status' => 'active',
                'rating' => 4.9,
                'total_deliveries' => 312,
                'successful_deliveries' => 305,
                'current_latitude' => 22.624768,
                'current_longitude' => 72.855568,
                'current_location' => 'Anand, Gujarat',
                'city' => 'Anand',
                'state' => 'Gujarat',
                'pincode' => '388001',
                'vehicle_type' => 'Motorcycle',
                'vehicle_number' => 'GJ-02-CD-5678',
                'agent_code' => 'AG002',
                'is_online' => true,
                'is_active' => true
            ],
            [
                'name' => 'Priya Singh',
                'email' => 'priya.singh@delivery.com',
                'mobile' => '9876543212',
                'password' => 'password123',
                'role' => 'delivery_agent',
                'status' => 'active',
                'rating' => 4.7,
                'total_deliveries' => 178,
                'successful_deliveries' => 170,
                'current_latitude' => 22.424768,
                'current_longitude' => 73.055568,
                'current_location' => 'Vadodara, Gujarat',
                'city' => 'Vadodara',
                'state' => 'Gujarat',
                'pincode' => '390001',
                'vehicle_type' => 'Scooter',
                'vehicle_number' => 'GJ-06-EF-9012',
                'agent_code' => 'AG003',
                'is_online' => true,
                'is_active' => true
            ],
            [
                'name' => 'Vikram Mehta',
                'email' => 'vikram.mehta@delivery.com',
                'mobile' => '9876543213',
                'password' => 'password123',
                'role' => 'delivery_agent',
                'status' => 'active',
                'rating' => 4.6,
                'total_deliveries' => 98,
                'successful_deliveries' => 92,
                'current_latitude' => 22.724768,
                'current_longitude' => 72.755568,
                'current_location' => 'Gandhinagar, Gujarat',
                'city' => 'Gandhinagar',
                'state' => 'Gujarat',
                'pincode' => '382010',
                'vehicle_type' => 'Bicycle',
                'vehicle_number' => 'GJ-18-GH-3456',
                'agent_code' => 'AG004',
                'is_online' => true,
                'is_active' => true
            ],
            [
                'name' => 'Neha Gupta',
                'email' => 'neha.gupta@delivery.com',
                'mobile' => '9876543214',
                'password' => 'password123',
                'role' => 'delivery_agent',
                'status' => 'active',
                'rating' => 4.9,
                'total_deliveries' => 456,
                'successful_deliveries' => 450,
                'current_latitude' => 22.824768,
                'current_longitude' => 72.655568,
                'current_location' => 'Surat, Gujarat',
                'city' => 'Surat',
                'state' => 'Gujarat',
                'pincode' => '395001',
                'vehicle_type' => 'Motorcycle',
                'vehicle_number' => 'GJ-05-IJ-7890',
                'agent_code' => 'AG005',
                'is_online' => false,
                'is_active' => true
            ],
            [
                'name' => 'Rajesh Kumar',
                'email' => 'rajesh.kumar@example.com',
                'mobile' => '9876543215',
                'password' => 'password123',
                'role' => 'delivery_agent',
                'status' => 'active',
                'rating' => 4.5,
                'total_deliveries' => 67,
                'successful_deliveries' => 60,
                'current_latitude' => 22.524768,
                'current_longitude' => 72.955568,
                'current_location' => 'Ahmedabad, Gujarat',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'pincode' => '380002',
                'vehicle_type' => 'Motorcycle',
                'vehicle_number' => 'GJ-01-KL-1234',
                'agent_code' => 'AG006',
                'is_online' => true,
                'is_active' => true
            ]
        ];

        DB::beginTransaction();

        try {
            foreach ($agents as $agentData) {
                // Extract user data and delivery agent data
                $userData = [
                    'name' => $agentData['name'],
                    'email' => $agentData['email'],
                    'mobile' => $agentData['mobile'],
                    'password' => Hash::make($agentData['password']),
                    'role' => $agentData['role'],
                    'status' => $agentData['status'],
                    'rating' => $agentData['rating'],
                    'total_deliveries' => $agentData['total_deliveries'],
                    'current_latitude' => $agentData['current_latitude'],
                    'current_longitude' => $agentData['current_longitude'],
                    'current_location' => $agentData['current_location'],
                    'email_verified_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                // Check if user already exists
                $user = User::where('email', $agentData['email'])->first();

                if ($user) {
                    // Update existing user
                    $user->update($userData);
                    $this->command->info("Updated existing user: {$agentData['name']}");
                } else {
                    // Create new user
                    $user = User::create($userData);
                    $this->command->info("Created new user: {$agentData['name']}");
                }

                // Prepare delivery agent data
                $deliveryAgentData = [
                    'user_id' => $user->id,
                    'name' => $agentData['name'],
                    'phone' => $agentData['mobile'],
                    'email' => $agentData['email'],
                    'agent_code' => $agentData['agent_code'],
                    'vehicle_type' => $agentData['vehicle_type'],
                    'vehicle_number' => $agentData['vehicle_number'],
                    'current_latitude' => $agentData['current_latitude'],
                    'current_longitude' => $agentData['current_longitude'],
                    'current_location' => $agentData['current_location'],
                    'city' => $agentData['city'],
                    'state' => $agentData['state'],
                    'pincode' => $agentData['pincode'],
                    'status' => $agentData['is_online'] ? 'available' : 'offline',
                    'rating' => $agentData['rating'],
                    'total_deliveries' => $agentData['total_deliveries'],
                    'successful_deliveries' => $agentData['successful_deliveries'],
                    'is_active' => $agentData['is_active'],
                    'is_online' => $agentData['is_online'],
                    'last_active_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                // Check if delivery agent already exists
                $deliveryAgent = DeliveryAgent::where('user_id', $user->id)->first();

                if ($deliveryAgent) {
                    // Update existing delivery agent
                    $deliveryAgent->update($deliveryAgentData);
                    $this->command->info("Updated delivery agent: {$agentData['name']}");
                } else {
                    // Create new delivery agent
                    DeliveryAgent::create($deliveryAgentData);
                    $this->command->info("Created delivery agent: {$agentData['name']}");
                }
            }

            DB::commit();

            $this->command->info('');
            $this->command->info('✅ =====================================');
            $this->command->info('✅ Agent Seeder Completed Successfully!');
            $this->command->info('✅ =====================================');
            $this->command->info('');
            $this->command->info('📊 Summary:');
            $this->command->info('   - Total Agents: ' . count($agents));
            $this->command->info('   - Users Table: Updated/Created');
            $this->command->info('   - Delivery Agents Table: Updated/Created');
            $this->command->info('');
            $this->command->info('🚚 Delivery Agents Added:');

            foreach ($agents as $agent) {
                $this->command->info("   • {$agent['name']} ({$agent['agent_code']}) - {$agent['city']}");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error seeding agents: ' . $e->getMessage());
            $this->command->error('   Trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
}
