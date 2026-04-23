<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveHolidaySeeder extends Seeder
{
    public function run(): void
    {
        $year = date('Y');

        $holidays = [
            [
                'name' => 'New Year\'s Day',
                'date' => $year . '-01-01',
                'type' => 'public',
                'repeats_annually' => true,
                'applicable_to' => 'all',
                'description' => 'New Year Celebration',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Republic Day',
                'date' => $year . '-01-26',
                'type' => 'public',
                'repeats_annually' => true,
                'applicable_to' => 'all',
                'description' => 'Republic Day of India',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Holi',
                'date' => $year . '-03-08',
                'type' => 'public',
                'repeats_annually' => true,
                'applicable_to' => 'all',
                'description' => 'Festival of Colors',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Independence Day',
                'date' => $year . '-08-15',
                'type' => 'public',
                'repeats_annually' => true,
                'applicable_to' => 'all',
                'description' => 'Independence Day of India',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Gandhi Jayanti',
                'date' => $year . '-10-02',
                'type' => 'public',
                'repeats_annually' => true,
                'applicable_to' => 'all',
                'description' => 'Birth Anniversary of Mahatma Gandhi',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Diwali',
                'date' => $year . '-11-01',
                'type' => 'public',
                'repeats_annually' => true,
                'applicable_to' => 'all',
                'description' => 'Festival of Lights',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Christmas',
                'date' => $year . '-12-25',
                'type' => 'public',
                'repeats_annually' => true,
                'applicable_to' => 'all',
                'description' => 'Christmas Day',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Company Foundation Day',
                'date' => $year . '-05-15',
                'type' => 'company',
                'repeats_annually' => true,
                'applicable_to' => 'all',
                'description' => 'Company Establishment Day',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($holidays as $holiday) {
            DB::table('leave_holidays')->updateOrInsert(
                ['name' => $holiday['name'], 'date' => $holiday['date']],
                $holiday
            );
        }

        $this->command->info('Leave holidays seeded successfully!');
    }
}
