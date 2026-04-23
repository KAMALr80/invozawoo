<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeavePolicySeeder extends Seeder
{
    public function run(): void
    {
        $policies = [
            [
                'name' => 'Annual Leave Policy',
                'leave_type' => 'annual',
                'days_per_year' => 21,
                'accrual_method' => 'monthly',
                'carry_forward_allowed' => true,
                'max_carry_forward_days' => 10,
                'min_service_days' => 90,
                'applicable_gender' => 'all',
                'is_paid' => true,
                'max_consecutive_days' => 20,
                'min_notice_days' => 7,
                'requires_approval' => true,
                'requires_document' => false,
                'requires_handover' => true,
                'is_active' => true,
                'effective_from' => Carbon::now()->startOfYear(),
                'description' => 'Annual leave for all employees - 21 days per year, carries forward up to 10 days',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Sick Leave Policy',
                'leave_type' => 'sick',
                'days_per_year' => 14,
                'accrual_method' => 'lump_sum',
                'carry_forward_allowed' => false,
                'max_carry_forward_days' => 0,
                'min_service_days' => 30,
                'applicable_gender' => 'all',
                'is_paid' => true,
                'max_consecutive_days' => 5,
                'min_notice_days' => 0,
                'requires_approval' => true,
                'requires_document' => true,
                'requires_handover' => false,
                'is_active' => true,
                'effective_from' => Carbon::now()->startOfYear(),
                'description' => 'Sick leave with medical certificate required for more than 3 consecutive days',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Casual Leave Policy',
                'leave_type' => 'casual',
                'days_per_year' => 7,
                'accrual_method' => 'lump_sum',
                'carry_forward_allowed' => false,
                'max_carry_forward_days' => 0,
                'min_service_days' => 60,
                'applicable_gender' => 'all',
                'is_paid' => true,
                'max_consecutive_days' => 3,
                'min_notice_days' => 1,
                'requires_approval' => true,
                'requires_document' => false,
                'requires_handover' => false,
                'is_active' => true,
                'effective_from' => Carbon::now()->startOfYear(),
                'description' => 'Casual leave for personal reasons - 7 days per year',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Maternity Leave Policy',
                'leave_type' => 'maternity',
                'days_per_year' => 180,
                'accrual_method' => 'lump_sum',
                'carry_forward_allowed' => false,
                'max_carry_forward_days' => 0,
                'min_service_days' => 180,
                'applicable_gender' => 'female',
                'is_paid' => true,
                'max_consecutive_days' => 180,
                'min_notice_days' => 30,
                'requires_approval' => true,
                'requires_document' => true,
                'requires_handover' => true,
                'is_active' => true,
                'effective_from' => Carbon::now()->startOfYear(),
                'description' => 'Maternity leave as per government rules - 180 days',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Paternity Leave Policy',
                'leave_type' => 'paternity',
                'days_per_year' => 15,
                'accrual_method' => 'lump_sum',
                'carry_forward_allowed' => false,
                'max_carry_forward_days' => 0,
                'min_service_days' => 180,
                'applicable_gender' => 'male',
                'is_paid' => true,
                'max_consecutive_days' => 15,
                'min_notice_days' => 15,
                'requires_approval' => true,
                'requires_document' => true,
                'requires_handover' => true,
                'is_active' => true,
                'effective_from' => Carbon::now()->startOfYear(),
                'description' => 'Paternity leave for new fathers - 15 days',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Bereavement Leave Policy',
                'leave_type' => 'bereavement',
                'days_per_year' => 5,
                'accrual_method' => 'lump_sum',
                'carry_forward_allowed' => false,
                'max_carry_forward_days' => 0,
                'min_service_days' => 0,
                'applicable_gender' => 'all',
                'is_paid' => true,
                'max_consecutive_days' => 5,
                'min_notice_days' => 0,
                'requires_approval' => true,
                'requires_document' => true,
                'requires_handover' => false,
                'is_active' => true,
                'effective_from' => Carbon::now()->startOfYear(),
                'description' => 'Leave in case of family bereavement - 5 days per year',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Study Leave Policy',
                'leave_type' => 'study',
                'days_per_year' => 10,
                'accrual_method' => 'lump_sum',
                'carry_forward_allowed' => false,
                'max_carry_forward_days' => 0,
                'min_service_days' => 365,
                'applicable_gender' => 'all',
                'is_paid' => true,
                'max_consecutive_days' => 5,
                'min_notice_days' => 15,
                'requires_approval' => true,
                'requires_document' => true,
                'requires_handover' => true,
                'is_active' => true,
                'effective_from' => Carbon::now()->startOfYear(),
                'description' => 'Leave for examinations and studies - 10 days per year',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Unpaid Leave Policy',
                'leave_type' => 'unpaid',
                'days_per_year' => null,
                'accrual_method' => 'lump_sum',
                'carry_forward_allowed' => false,
                'max_carry_forward_days' => 0,
                'min_service_days' => 0,
                'applicable_gender' => 'all',
                'is_paid' => false,
                'max_consecutive_days' => 30,
                'min_notice_days' => 3,
                'requires_approval' => true,
                'requires_document' => false,
                'requires_handover' => true,
                'is_active' => true,
                'effective_from' => Carbon::now()->startOfYear(),
                'description' => 'Unpaid leave when paid leaves are exhausted',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($policies as $policy) {
            DB::table('leave_policies')->updateOrInsert(
                ['leave_type' => $policy['leave_type']],
                $policy
            );
        }

        $this->command->info('Leave policies seeded successfully!');
    }
}
