<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Employee::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️ No users found. Skipping EmployeeSeeder.');
            return;
        }

        // Sirf wo columns jo employees table mein EXIST karte hain
        $employees = [
            [
                'user_id' => $users->where('email', 'admin@example.com')->first()?->id ?? 1,
                'employee_code' => 'EMP001',
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'department' => 'Management',
                'phone' => '9876543001',
                'joining_date' => '2024-01-01',
                'status' => 1,
            ],
            [
                'user_id' => $users->where('email', 'hr@example.com')->first()?->id ?? 2,
                'employee_code' => 'EMP002',
                'name' => 'HR Manager',
                'email' => 'hr@example.com',
                'department' => 'Human Resources',
                'phone' => '9876543002',
                'joining_date' => '2024-01-15',
                'status' => 1,
            ],
            [
                'user_id' => $users->where('email', 'staff@example.com')->first()?->id ?? 3,
                'employee_code' => 'EMP003',
                'name' => 'Sales Executive',
                'email' => 'staff@example.com',
                'department' => 'Sales',
                'phone' => '9876543003',
                'joining_date' => '2024-02-01',
                'status' => 1,
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }

        $this->command->info('✅ Employees seeded successfully!');
    }
}
