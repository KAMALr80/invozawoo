<?php

namespace App\Helpers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class AttendanceLeaveHelper
{
    /**
     * Get attendance and leave report for employee
     *
     * @param Employee $employee
     * @param int $year
     * @param int $month
     * @return array
     */
    public static function getMonthlyReport(Employee $employee, int $year, int $month): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $attendances = $employee->attendances()
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date')
            ->get();

        $leaves = $employee->leaves()
            ->where('status', 'Approved')
            ->whereBetween('from_date', [$startDate, $endDate])
            ->get();

        return [
            'employee' => $employee,
            'month' => $month,
            'year' => $year,
            'attendances' => $attendances,
            'leaves' => $leaves,
            'statistics' => self::calculateStatistics($employee, $startDate, $endDate),
        ];
    }

    /**
     * Calculate attendance statistics
     *
     * @param Employee $employee
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private static function calculateStatistics(Employee $employee, Carbon $startDate, Carbon $endDate): array
    {
        $attendances = $employee->attendances()
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        $presents = $attendances->where('status', 'Present')->count();
        $absents = $attendances->where('status', 'Absent')->count();
        $lates = $attendances->where('status', 'Late')->count();
        $halfDays = $attendances->where('status', 'Half Day')->count();
        $onLeaves = $attendances->where('status', 'On Leave')->count();

        $totalWorkingDays = self::getTotalWorkingDays($startDate, $endDate);
        $attendancePercentage = $totalWorkingDays > 0
            ? round(($presents / $totalWorkingDays) * 100, 2)
            : 0;

        $totalWorkingHours = 0;
        foreach ($attendances as $attendance) {
            if ($attendance->net_working_hours) {
                $totalWorkingHours += self::parseTimeToHours($attendance->net_working_hours);
            }
        }

        return [
            'presents' => $presents,
            'absents' => $absents,
            'lates' => $lates,
            'half_days' => $halfDays,
            'on_leaves' => $onLeaves,
            'total_working_days' => $totalWorkingDays,
            'attendance_percentage' => $attendancePercentage,
            'total_working_hours' => round($totalWorkingHours, 2),
            'average_daily_hours' => $presents > 0
                ? round($totalWorkingHours / $presents, 2)
                : 0,
        ];
    }

    /**
     * Get total working days in date range (excluding weekends and holidays)
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    public static function getTotalWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $workingDays = 0;
        $current = $startDate->copy();

        while ($current <= $endDate) {
            // Skip weekends
            if (!in_array($current->dayOfWeek, [0, 6])) {
                // Skip holidays
                if (!Attendance::isHoliday($current->toDateString())) {
                    $workingDays++;
                }
            }
            $current->addDay();
        }

        return $workingDays;
    }

    /**
     * Parse time string to decimal hours
     *
     * @param string $time
     * @return float
     */
    private static function parseTimeToHours(string $time): float
    {
        if (!preg_match('/(\d{2}):(\d{2}):(\d{2})/', $time, $matches)) {
            return 0;
        }

        $hours = (int)$matches[1];
        $minutes = (int)$matches[2];

        return $hours + ($minutes / 60);
    }

    /**
     * Check if employee is on leave for date range
     *
     * @param Employee $employee
     * @param string $fromDate
     * @param string $toDate
     * @return Collection
     */
    public static function getLeavesBetween(Employee $employee, string $fromDate, string $toDate): Collection
    {
        return $employee->leaves()
            ->where('status', 'Approved')
            ->whereBetween('from_date', [$fromDate, $toDate])
            ->orWhereBetween('to_date', [$fromDate, $toDate])
            ->get();
    }

    /**
     * Get pending approval leaves by department
     *
     * @param string $department
     * @return Collection
     */
    public static function getPendingLeavesByDepartment(string $department): Collection
    {
        return Leave::whereHas('employee', function ($query) use ($department) {
            $query->where('department', $department);
        })
            ->where('status', 'Pending')
            ->orderBy('applied_on', 'desc')
            ->get();
    }

    /**
     * Get leave balance status for employees
     *
     * @param int $departmentId
     * @return Collection
     */
    public static function getDepartmentLeaveStatus(int $departmentId): Collection
    {
        $employees = Employee::where('department', $departmentId)->get();

        return $employees->map(function ($employee) {
            return [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'employee_code' => $employee->employee_code,
                'leave_balance' => $employee->leaveBalances()
                    ->where('year', now()->year)
                    ->get()
                    ->keyBy('leave_type'),
            ];
        });
    }

    /**
     * Generate attendance improvement suggestions
     *
     * @param Employee $employee
     * @param int $month
     * @param int $year
     * @return array
     */
    public static function getAttendanceSuggestions(Employee $employee, int $month, int $year): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $attendances = $employee->attendances()
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        $lates = $attendances->where('status', 'Late')->count();
        $halfDays = $attendances->where('status', 'Half Day')->count();
        $absents = $attendances->where('status', 'Absent')->count();

        $suggestions = [];

        if ($lates > 3) {
            $suggestions[] = "High number of late check-ins ($lates). Consider reviewing punctuality.";
        }

        if ($halfDays > 2) {
            $suggestions[] = "Multiple half-day attendances ($halfDays). Ensure work-life balance.";
        }

        if ($absents > 2) {
            $suggestions[] = "Multiple absences ($absents). Review with HR if needed.";
        }

        if ($lates === 0 && $absents === 0) {
            $suggestions[] = "Perfect attendance record! Keep it up.";
        }

        return $suggestions;
    }

    /**
     * Get employee dashboard summary
     *
     * @param Employee $employee
     * @return array
     */
    public static function getDashboardSummary(Employee $employee): array
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $thisMonthStart = Carbon::createFromDate($currentYear, $currentMonth, 1);
        $thisMonthEnd = $thisMonthStart->copy()->endOfMonth();

        $thisMonthAttendance = $employee->attendances()
            ->whereBetween('attendance_date', [$thisMonthStart, $thisMonthEnd])
            ->get();

        $thisMonthLeaves = $employee->leaves()
            ->where('status', 'Approved')
            ->whereBetween('from_date', [$thisMonthStart, $thisMonthEnd])
            ->get();

        $leaveBalance = $employee->leaveBalances()
            ->where('year', $currentYear)
            ->get()
            ->keyBy('leave_type');

        return [
            'employee' => $employee,
            'this_month_attendance_count' => $thisMonthAttendance->count(),
            'this_month_presents' => $thisMonthAttendance->where('status', 'Present')->count(),
            'this_month_absents' => $thisMonthAttendance->where('status', 'Absent')->count(),
            'this_month_lates' => $thisMonthAttendance->where('status', 'Late')->count(),
            'this_month_on_leave' => $thisMonthLeaves->count(),
            'total_leave_days_this_month' => $thisMonthLeaves->sum('total_days'),
            'leave_balances' => $leaveBalance,
            'total_remaining_leaves' => $leaveBalance->sum('remaining'),
        ];
    }
}
