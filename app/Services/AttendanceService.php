<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\LeaveHoliday;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AttendanceService
{
    /**
     * Mark attendance for an employee
     *
     * @param Employee $employee
     * @param array $data
     * @return Attendance
     * @throws \Exception
     */
    public function markAttendance(Employee $employee, array $data): Attendance
    {
        // Validate against leave
        $validation = $this->validateAttendanceAgainstLeave(
            $employee->id,
            $data['attendance_date'] ?? now()->toDateString()
        );

        if (!$validation['valid']) {
            throw new \Exception($validation['message']);
        }

        // Create or update attendance
        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => $data['attendance_date'] ?? now()->toDateString(),
            ],
            $data
        );

        return $attendance;
    }

    /**
     * Validate attendance marking against leave
     *
     * @param int $employeeId
     * @param string $date
     * @return array
     */
    public function validateAttendanceAgainstLeave(int $employeeId, string $date): array
    {
        // Check if employee is on approved leave
        if ($this->isEmployeeOnLeave($employeeId, $date)) {
            $leave = $this->getLeaveForDate($employeeId, $date);
            return [
                'valid' => false,
                'message' => "Employee is on approved {$leave->leave_type_label} leave. Cannot mark attendance.",
            ];
        }

        // Check for weekend
        if ($this->isWeekend($date)) {
            return [
                'valid' => false,
                'message' => "Cannot mark attendance on weekend.",
            ];
        }

        // Check for holiday
        if ($this->isHoliday($date)) {
            $holiday = $this->getHolidayForDate($date);
            return [
                'valid' => false,
                'message' => "Cannot mark attendance on holiday: {$holiday->name}",
            ];
        }

        return ['valid' => true];
    }

    /**
     * Check if employee is on leave for a given date
     *
     * @param int $employeeId
     * @param string $date
     * @return bool
     */
    public function isEmployeeOnLeave(int $employeeId, string $date): bool
    {
        return Leave::where('employee_id', $employeeId)
            ->where('status', 'Approved')
            ->whereDate('from_date', '<=', $date)
            ->whereDate('to_date', '>=', $date)
            ->exists();
    }

    /**
     * Get leave record for a date
     *
     * @param int $employeeId
     * @param string $date
     * @return Leave|null
     */
    public function getLeaveForDate(int $employeeId, string $date): ?Leave
    {
        return Leave::where('employee_id', $employeeId)
            ->where('status', 'Approved')
            ->whereDate('from_date', '<=', $date)
            ->whereDate('to_date', '>=', $date)
            ->first();
    }

    /**
     * Check if date is a weekend
     *
     * @param string $date
     * @return bool
     */
    public function isWeekend(string $date): bool
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        return in_array($dayOfWeek, [0, 6]); // 0 = Sunday, 6 = Saturday
    }

    /**
     * Check if date is a holiday
     *
     * @param string $date
     * @return bool
     */
    public function isHoliday(string $date): bool
    {
        return LeaveHoliday::where('date', $date)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get holiday record for a date
     *
     * @param string $date
     * @return LeaveHoliday|null
     */
    public function getHolidayForDate(string $date): ?LeaveHoliday
    {
        return LeaveHoliday::where('date', $date)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Auto-mark attendance for approved leave
     *
     * @param Leave $leave
     * @return void
     */
    public function autoMarkLeaveAttendance(Leave $leave): void
    {
        if (!$leave->isApproved()) {
            return;
        }

        foreach ($leave->getDateRange() as $date) {
            // Skip if attendance already exists
            if (Attendance::where('employee_id', $leave->employee_id)
                ->where('attendance_date', $date)
                ->exists()) {
                continue;
            }

            // Skip weekends
            if ($this->isWeekend($date)) {
                continue;
            }

            // Skip holidays
            if ($this->isHoliday($date)) {
                continue;
            }

            Attendance::create([
                'employee_id' => $leave->employee_id,
                'leave_id' => $leave->id,
                'attendance_date' => $date,
                'status' => Attendance::STATUS_ON_LEAVE,
                'is_auto_marked' => true,
                'remarks' => "Auto-marked: {$leave->leave_type_label} leave (Ref: {$leave->leave_number})"
            ]);
        }
    }

    /**
     * Remove auto-marked leave attendance
     *
     * @param Leave $leave
     * @return void
     */
    public function removeLeaveAttendance(Leave $leave): void
    {
        $leave->attendances()
            ->where('is_auto_marked', true)
            ->where('status', Attendance::STATUS_ON_LEAVE)
            ->delete();
    }

    /**
     * Get attendance summary for employee
     *
     * @param Employee $employee
     * @param string $fromDate
     * @param string $toDate
     * @return array
     */
    public function getAttendanceSummary(Employee $employee, string $fromDate, string $toDate): array
    {
        $attendances = $employee->attendances()
            ->whereBetween('attendance_date', [$fromDate, $toDate])
            ->get();

        $summary = [
            'total_days' => 0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'half_day' => 0,
            'on_leave' => 0,
            'working_hours' => 0,
        ];

        foreach ($attendances as $attendance) {
            $summary['total_days']++;

            match (strtolower($attendance->status)) {
                'present' => $summary['present']++,
                'absent' => $summary['absent']++,
                'late' => $summary['late']++,
                'half day' => $summary['half_day']++,
                'on leave' => $summary['on_leave']++,
                default => null
            };

            if ($attendance->net_working_hours) {
                $summary['working_hours'] += $this->parseHoursToDecimal($attendance->net_working_hours);
            }
        }

        return $summary;
    }

    /**
     * Convert time format HH:MM:SS to decimal hours
     *
     * @param string $time
     * @return float
     */
    private function parseHoursToDecimal(string $time): float
    {
        if (!preg_match('/(\d{2}):(\d{2}):(\d{2})/', $time, $matches)) {
            return 0;
        }

        $hours = (int)$matches[1];
        $minutes = (int)$matches[2];

        return $hours + ($minutes / 60);
    }

    /**
     * Get leaves in date range
     *
     * @param Employee $employee
     * @param string $fromDate
     * @param string $toDate
     * @return Collection
     */
    public function getLeavesSummary(Employee $employee, string $fromDate, string $toDate): Collection
    {
        return $employee->leaves()
            ->where('status', 'Approved')
            ->whereBetween('from_date', [$fromDate, $toDate])
            ->get()
            ->groupBy('leave_type')
            ->mapWithKeys(function ($group, $leaveType) {
                return [
                    $leaveType => [
                        'count' => $group->count(),
                        'total_days' => $group->sum('total_days'),
                        'leaves' => $group->values()
                    ]
                ];
            });
    }

    /**
     * Calculate attendance percentage
     *
     * @param Employee $employee
     * @param int $month
     * @param int $year
     * @return float
     */
    public function calculateAttendancePercentage(Employee $employee, int $month, int $year): float
    {
        $totalWorkingDays = $this->getTotalWorkingDaysInMonth($month, $year);

        $presentDays = $employee->attendances()
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->whereIn('status', ['Present', 'Late'])
            ->count();

        if ($totalWorkingDays === 0) {
            return 0;
        }

        return round(($presentDays / $totalWorkingDays) * 100, 2);
    }

    /**
     * Get total working days in a month (excluding weekends and holidays)
     *
     * @param int $month
     * @param int $year
     * @return int
     */
    private function getTotalWorkingDaysInMonth(int $month, int $year): int
    {
        $start = Carbon::createFromDate($year, $month, 1);
        $end = $start->copy()->endOfMonth();

        $workingDays = 0;
        $current = $start->copy();

        while ($current <= $end) {
            // Skip weekends
            if (!in_array($current->dayOfWeek, [0, 6])) {
                // Skip holidays
                if (!$this->isHoliday($current->toDateString())) {
                    $workingDays++;
                }
            }
            $current->addDay();
        }

        return $workingDays;
    }
}
