<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;



class MarkAbsent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mark-absent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */


    public function handle()
{
    $employees = Employee::all();

    foreach ($employees as $employee) {
        Attendance::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => today(),
            ],
            [
                'status' => 'Absent',
                'remarks' => 'No Check-in',
            ]
        );
    }
}

}
