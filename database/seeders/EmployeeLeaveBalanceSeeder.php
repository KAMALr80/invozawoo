<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use Carbon\Carbon;

class EmployeeLeaveBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $year = date('Y');

        if ($employees->isEmpty()) {
            $this->command->info('No employees found. Skipping leave balance seeding.');
            return;
        }

        $leaveTypes = ['annual', 'sick', 'casual'];
        $policies = DB::table('leave_policies')->whereIn('leave_type', $leaveTypes)->get()->keyBy('leave_type');

        $inserted = 0;
        $updated = 0;

        foreach ($employees as $employee) {
            foreach ($leaveTypes as $leaveType) {
                $policy = $policies->get($leaveType);
                $entitled = $policy ? $policy->days_per_year : 0;

                // Calculate joining date prorata if employee joined this year
                $joiningDate = $employee->joining_date ? Carbon::parse($employee->joining_date) : Carbon::now()->startOfYear();
                $monthsWorked = max(1, $joiningDate->diffInMonths(Carbon::now()) + 1);

                if ($joiningDate->year == $year) {
                    $entitled = round(($entitled / 12) * $monthsWorked, 1);
                }

                // Check if record already exists
                $existing = DB::table('leave_balances')
                    ->where('employee_id', $employee->id)
                    ->where('year', $year)
                    ->where('leave_type', $leaveType)
                    ->first();

                if ($existing) {
                    // Update existing record
                    DB::table('leave_balances')
                        ->where('id', $existing->id)
                        ->update([
                            'entitled' => $entitled,
                            'total_available' => $entitled + $existing->carry_forward,
                            'remaining' => ($entitled + $existing->carry_forward) - $existing->used,
                            'updated_at' => now()
                        ]);
                    $updated++;
                } else {
                    // Insert new record
                    DB::table('leave_balances')->insert([
                        'employee_id' => $employee->id,
                        'year' => $year,
                        'leave_type' => $leaveType,
                        'entitled' => $entitled,
                        'used' => 0,
                        'pending' => 0,
                        'remaining' => $entitled,
                        'carry_forward' => 0,
                        'total_available' => $entitled,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $inserted++;
                }
            }
        }

        $this->command->info("Employee leave balances seeded successfully! Inserted: {$inserted}, Updated: {$updated}");
    }
}
