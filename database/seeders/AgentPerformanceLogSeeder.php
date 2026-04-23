<?php

namespace Database\Seeders;

use App\Models\AgentPerformanceLog;
use App\Models\DeliveryAgent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgentPerformanceLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AgentPerformanceLog::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $agents = DeliveryAgent::all();

        if ($agents->isEmpty()) {
            $this->command->warn('⚠️ No delivery agents found. Skipping AgentPerformanceLogSeeder.');
            return;
        }

        // Generate logs for last 30 days
        $startDate = Carbon::now()->subDays(30);

        foreach ($agents as $agent) {
            for ($i = 0; $i < 30; $i++) {
                $date = $startDate->copy()->addDays($i);

                // Skip some days for variety (70% active rate)
                if (rand(1, 100) > 70) {
                    continue;
                }

                $assigned = rand(5, 15);
                $delivered = rand(4, $assigned);
                $failed = $assigned - $delivered;

                $distance = rand(20, 50) + (rand(0, 30));
                $timeMinutes = rand(120, 300) + (rand(0, 120));
                $activeMinutes = rand(180, 360);

                $basePay = $agent->salary / 30; // Daily base pay
                $commission = $delivered * ($agent->commission_value ?? 5);
                $bonus = $delivered > 10 ? rand(50, 100) : 0;

                $firstActive = $date->copy()->setTime(rand(8, 10), rand(0, 59));
                $lastActive = $date->copy()->setTime(rand(17, 19), rand(0, 59));

                $log = AgentPerformanceLog::create([
                    'agent_id' => $agent->id,
                    'log_date' => $date,
                    'shipments_assigned' => $assigned,
                    'shipments_delivered' => $delivered,
                    'shipments_failed' => $failed,
                    'total_distance_km' => $distance,
                    'total_time_minutes' => $timeMinutes,
                    'average_rating' => rand(35, 50) / 10, // 3.5 to 5.0
                    'first_active_at' => $firstActive,
                    'last_active_at' => $lastActive,
                    'active_minutes' => $activeMinutes,
                    'base_pay' => $basePay,
                    'commission_earned' => $commission,
                    'bonus_earned' => $bonus,
                    'total_earnings' => $basePay + $commission + $bonus,
                ]);

                if ($i === 0) {
                    $this->command->info("✅ Performance logs generated for agent: {$agent->name}");
                }
            }
        }

        $totalLogs = AgentPerformanceLog::count();
        $this->command->info("✅ {$totalLogs} agent performance logs seeded successfully!");
    }
}
