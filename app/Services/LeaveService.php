<?php

namespace App\Services;

use App\Models\Leave;
use App\Models\LeaveBalance;
use App\Models\LeavePolicy;
use App\Models\LeaveHoliday;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class LeaveService
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Request leave for an employee
     *
     * @param Employee $employee
     * @param array $data
     * @return Leave
     * @throws \Exception
     */
    public function requestLeave(Employee $employee, array $data): Leave
    {
        // Validate leave request
        $validation = $this->validateLeaveRequest($employee, $data);
        if (!$validation['valid']) {
            throw new \Exception($validation['message']);
        }

        // Calculate total days
        $totalDays = $this->calculateLeaveDays(
            $data['from_date'],
            $data['to_date'],
            $data['duration_type'] ?? 'full_day'
        );

        // Create leave record
        $leave = Leave::create(array_merge($data, [
            'employee_id' => $employee->id,
            'total_days' => $totalDays,
            'status' => 'Pending',
            'applied_on' => now(),
            'leave_number' => $this->generateLeaveNumber()
        ]));

        return $leave;
    }

    /**
     * Validate leave request
     *
     * @param Employee $employee
     * @param array $data
     * @return array
     */
    public function validateLeaveRequest(Employee $employee, array $data): array
    {
        // Check required fields
        if (empty($data['from_date']) || empty($data['to_date'])) {
            return ['valid' => false, 'message' => 'From and To dates are required.'];
        }

        $fromDate = Carbon::parse($data['from_date']);
        $toDate = Carbon::parse($data['to_date']);

        // Validate date range
        if ($fromDate > $toDate) {
            return ['valid' => false, 'message' => 'From date cannot be after To date.'];
        }

        // Check for past dates
        if ($toDate < now()->startOfDay()) {
            return ['valid' => false, 'message' => 'Cannot apply leave for past dates.'];
        }

        // Check for minimum notice period
        $policy = LeavePolicy::where('leave_type', $data['leave_type'] ?? 'casual')
            ->where('is_active', true)
            ->first();

        if ($policy && $policy->min_notice_days) {
            $minNoticeDate = now()->addDays($policy->min_notice_days);
            if ($fromDate < $minNoticeDate) {
                return [
                    'valid' => false,
                    'message' => "Minimum {$policy->min_notice_days} days notice required for {$policy->leave_type_label}"
                ];
            }
        }

        // Check for leave conflicts
        if ($this->hasLeaveConflicts($employee->id, $fromDate, $toDate)) {
            return ['valid' => false, 'message' => 'Leave period overlaps with existing approved leave.'];
        }

        // Check leave balance
        if (!$this->hasLeaveBalance($employee->id, $data['leave_type'] ?? 'casual')) {
            return ['valid' => false, 'message' => 'Insufficient leave balance.'];
        }

        // Check maximum consecutive days limit
        if ($policy && $policy->max_consecutive_days) {
            $totalDays = $fromDate->diffInDays($toDate) + 1;
            if ($totalDays > $policy->max_consecutive_days) {
                return [
                    'valid' => false,
                    'message' => "Cannot apply more than {$policy->max_consecutive_days} consecutive days for {$policy->leave_type_label}"
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Approve leave request
     *
     * @param Leave $leave
     * @param int $approvedBy
     * @param string $remarks
     * @return void
     * @throws \Exception
     */
    public function approveLeave(Leave $leave, int $approvedBy, string $remarks = ''): void
    {
        // Check balance before approval
        if (!$this->hasSufficientBalance($leave->employee_id, $leave->leave_type, $leave->total_days)) {
            throw new \Exception('Employee does not have sufficient leave balance.');
        }

        $leave->status = 'Approved';
        $leave->approved_by = $approvedBy;
        $leave->approved_at = now();
        $leave->approval_remarks = $remarks;
        $leave->save();

        // Update leave balance
        $this->deductLeaveBalance($leave->employee_id, $leave->leave_type, $leave->total_days);

        // Auto-mark attendance
        $this->attendanceService->autoMarkLeaveAttendance($leave);
    }

    /**
     * Reject leave request
     *
     * @param Leave $leave
     * @param int $rejectedBy
     * @param string $reason
     * @return void
     */
    public function rejectLeave(Leave $leave, int $rejectedBy, string $reason = ''): void
    {
        $leave->status = 'Rejected';
        $leave->rejected_by = $rejectedBy;
        $leave->rejected_at = now();
        $leave->rejection_reason = $reason;
        $leave->save();

        // Remove auto-marked attendance
        $this->attendanceService->removeLeaveAttendance($leave);
    }

    /**
     * Cancel approved leave
     *
     * @param Leave $leave
     * @param int $cancelledBy
     * @param string $reason
     * @return void
     * @throws \Exception
     */
    public function cancelLeave(Leave $leave, int $cancelledBy, string $reason = ''): void
    {
        if (!$leave->isApproved()) {
            throw new \Exception('Only approved leaves can be cancelled.');
        }

        $leave->status = 'Cancelled';
        $leave->cancelled_by = $cancelledBy;
        $leave->cancelled_at = now();
        $leave->save();

        // Reverse leave balance
        $this->reverseLeaveBalance($leave->employee_id, $leave->leave_type, $leave->total_days);

        // Remove auto-marked attendance
        $this->attendanceService->removeLeaveAttendance($leave);
    }

    /**
     * Calculate leave days based on duration type
     *
     * @param string $fromDate
     * @param string $toDate
     * @param string $durationType
     * @return int
     */
    public function calculateLeaveDays(string $fromDate, string $toDate, string $durationType = 'full_day'): int
    {
        $from = Carbon::parse($fromDate);
        $to = Carbon::parse($toDate);
        $days = 0;

        $current = $from->copy();

        while ($current <= $to) {
            // Skip weekends
            if (!in_array($current->dayOfWeek, [0, 6])) {
                // Skip holidays
                if (!$this->attendanceService->isHoliday($current->toDateString())) {
                    if ($durationType === 'half_day' && $current->isSameDay($from)) {
                        $days += 0.5;
                    } else {
                        $days++;
                    }
                }
            }
            $current->addDay();
        }

        return $days;
    }

    /**
     * Check if leave dates have conflicts
     *
     * @param int $employeeId
     * @param Carbon $fromDate
     * @param Carbon $toDate
     * @return bool
     */
    public function hasLeaveConflicts(int $employeeId, Carbon $fromDate, Carbon $toDate): bool
    {
        return Leave::where('employee_id', $employeeId)
            ->where('status', 'Approved')
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('from_date', [$fromDate, $toDate])
                    ->orWhereBetween('to_date', [$fromDate, $toDate])
                    ->orWhere(function ($q) use ($fromDate, $toDate) {
                        $q->where('from_date', '<=', $fromDate)
                          ->where('to_date', '>=', $toDate);
                    });
            })
            ->exists();
    }

    /**
     * Check if employee has sufficient leave balance
     *
     * @param int $employeeId
     * @param string $leaveType
     * @param float $days
     * @return bool
     */
    public function hasSufficientBalance(int $employeeId, string $leaveType, float $days): bool
    {
        $balance = LeaveBalance::where('employee_id', $employeeId)
            ->where('leave_type', $leaveType)
            ->where('year', now()->year)
            ->first();

        if (!$balance) {
            return false;
        }

        return $balance->remaining >= $days;
    }

    /**
     * Check if employee has leave balance (exists)
     *
     * @param int $employeeId
     * @param string $leaveType
     * @return bool
     */
    public function hasLeaveBalance(int $employeeId, string $leaveType): bool
    {
        return LeaveBalance::where('employee_id', $employeeId)
            ->where('leave_type', $leaveType)
            ->where('year', now()->year)
            ->where('remaining', '>', 0)
            ->exists();
    }

    /**
     * Deduct leave from balance
     *
     * @param int $employeeId
     * @param string $leaveType
     * @param float $days
     * @return void
     */
    public function deductLeaveBalance(int $employeeId, string $leaveType, float $days): void
    {
        $balance = LeaveBalance::where('employee_id', $employeeId)
            ->where('leave_type', $leaveType)
            ->where('year', now()->year)
            ->first();

        if ($balance) {
            $balance->used += $days;
            $balance->remaining = $balance->total_available - $balance->used - $balance->pending;
            $balance->save();
        }
    }

    /**
     * Reverse leave balance
     *
     * @param int $employeeId
     * @param string $leaveType
     * @param float $days
     * @return void
     */
    public function reverseLeaveBalance(int $employeeId, string $leaveType, float $days): void
    {
        $balance = LeaveBalance::where('employee_id', $employeeId)
            ->where('leave_type', $leaveType)
            ->where('year', now()->year)
            ->first();

        if ($balance) {
            $balance->used = max(0, $balance->used - $days);
            $balance->remaining = $balance->total_available - $balance->used - $balance->pending;
            $balance->save();
        }
    }

    /**
     * Generate unique leave number
     *
     * @return string
     */
    private function generateLeaveNumber(): string
    {
        $year = now()->year;
        $month = now()->month;
        $count = Leave::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;

        return "LV-{$year}-{$month}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get pending leaves for approval
     *
     * @param int $departmentId
     * @return Collection
     */
    public function getPendingLeaves(int $departmentId): Collection
    {
        return Leave::whereHas('employee', function ($query) use ($departmentId) {
            $query->where('department', $departmentId);
        })
            ->where('status', 'Pending')
            ->orderBy('applied_on', 'desc')
            ->get();
    }

    /**
     * Get employee leave summary
     *
     * @param Employee $employee
     * @return array
     */
    public function getEmployeeLeaveSummary(Employee $employee): array
    {
        $currentYear = now()->year;

        $balances = $employee->leaveBalances()
            ->where('year', $currentYear)
            ->get()
            ->keyBy('leave_type')
            ->map(function ($balance) {
                return [
                    'entitled' => $balance->entitled,
                    'used' => $balance->used,
                    'pending' => $balance->pending,
                    'remaining' => $balance->remaining,
                    'carry_forward' => $balance->carry_forward,
                    'total_available' => $balance->total_available,
                ];
            });

        return $balances->toArray();
    }

    /**
     * Get leaves for a date range
     *
     * @param int $employeeId
     * @param string $fromDate
     * @param string $toDate
     * @param string $status
     * @return Collection
     */
    public function getLeaves(int $employeeId, string $fromDate, string $toDate, string $status = 'Approved'): Collection
    {
        return Leave::where('employee_id', $employeeId)
            ->where('status', $status)
            ->whereBetween('from_date', [$fromDate, $toDate])
            ->orderBy('from_date')
            ->get();
    }
}
