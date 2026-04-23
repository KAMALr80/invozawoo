<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    // ✅ Specify table name (plural)
    protected $table = 'attendances';

    // Work timing constants
    const WORK_START_TIME = '15:00:00';  // 3:00 PM
    const WORK_END_TIME = '00:00:00';     // 12:00 AM (midnight)
    const GRACE_MINUTES = 15;              // 15 minutes grace period
    const LATE_AFTER = '15:15:00';        // 3:15 PM

    // Break details
    const BREAK_DURATION_MINUTES = 60;     // 1 hour total break
    const DINNER_BREAK_MINUTES = 45;       // 45 minutes dinner
    const TEA_BREAK_MINUTES = 15;          // 15 minutes tea

    // Required working hours
    const REQUIRED_WORKING_HOURS = 9;      // 9 hours net working

    // Attendance statuses
    const STATUS_PRESENT = 'Present';
    const STATUS_ABSENT = 'Absent';
    const STATUS_LATE = 'Late';
    const STATUS_HALF_DAY = 'Half Day';
    const STATUS_ON_LEAVE = 'On Leave';
    const STATUS_WEEKEND = 'Weekend';
    const STATUS_HOLIDAY = 'Holiday';

    protected $fillable = [
        'employee_id',
        'leave_id',
        'attendance_date',
        'check_in',
        'check_out',
        'working_hours',
        'break_minutes',
        'net_working_hours',
        'overtime_hours',
        'remarks',
        'status',
        'marked_by',
        'is_auto_marked',
        'check_in_ip',
        'check_out_ip'
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime:H:i:s',
        'check_out' => 'datetime:H:i:s',
        'is_auto_marked' => 'boolean',
        'break_minutes' => 'integer',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }

    public function marker()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    // Accessors
    public function getNetWorkingHoursAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        return $this->calculateNetWorkingHours();
    }

    public function getFormattedNetWorkingHoursAttribute()
    {
        $hours = $this->net_working_hours;
        if (!$hours) return '-';

        if (preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $hours, $matches)) {
            $h = (int)$matches[1];
            $m = (int)$matches[2];
            return $h . 'h ' . $m . 'm';
        }
        return $hours;
    }

    public function getStatusBadgeClassAttribute()
    {
        return match (strtolower($this->status)) {
            'present' => 'status-present',
            'absent' => 'status-absent',
            'late' => 'status-late',
            'half day' => 'status-halfday',
            'leave' => 'status-leave',
            default => 'status-unknown',
        };
    }

    public function getStatusWithEmojiAttribute()
    {
        return match (strtolower($this->status)) {
            'present' => '✅ Present',
            'absent' => '❌ Absent',
            'late' => '⏰ Late',
            'half day' => '⚠️ Half Day',
            'leave' => '🏖️ Leave',
            default => $this->status,
        };
    }

    // Helper methods
    public function calculateWorkingHours()
    {
        if (!$this->check_in || !$this->check_out) {
            return null;
        }

        $checkIn = Carbon::parse($this->check_in);
        $checkOut = Carbon::parse($this->check_out);

        // Handle midnight checkout
        if ($checkOut->lt($checkIn)) {
            $checkOut->addDay();
        }

        $totalSeconds = $checkIn->diffInSeconds($checkOut);
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function calculateNetWorkingHours()
    {
        $totalHours = $this->calculateWorkingHours();
        if (!$totalHours) return null;

        // Parse total hours
        preg_match('/(\d{2}):(\d{2}):(\d{2})/', $totalHours, $matches);
        $totalMinutes = ((int)$matches[1] * 60) + (int)$matches[2];

        // Subtract break time (1 hour = 60 minutes)
        $netMinutes = max(0, $totalMinutes - self::BREAK_DURATION_MINUTES);

        $hours = floor($netMinutes / 60);
        $minutes = $netMinutes % 60;

        return sprintf('%02d:%02d:00', $hours, $minutes);
    }

    public function determineStatus()
    {
        if (!$this->check_in) {
            return 'Absent';
        }

        $checkInTime = Carbon::parse($this->check_in);
        $lateTime = Carbon::createFromTimeString(self::LATE_AFTER);

        if ($checkInTime->gt($lateTime)) {
            return 'Late';
        }

        $netHours = $this->calculateNetWorkingHours();
        if ($netHours) {
            preg_match('/(\d{2}):(\d{2}):\d{2}/', $netHours, $matches);
            $workedHours = (int)$matches[1];

            if ($workedHours < 4) {
                return 'Half Day';
            }
        }

        return 'Present';
    }

    // Check if attendance is checked in
    public function isCheckedIn(): bool
    {
        return !is_null($this->check_in);
    }

    // Check if attendance is checked out
    public function isCheckedOut(): bool
    {
        return !is_null($this->check_out);
    }

    // Check if this record was auto-marked
    public function isAutoMarked(): bool
    {
        return $this->is_auto_marked;
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', today());
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('attendance_date', $year)
                     ->whereMonth('attendance_date', $month);
    }

    /* ==================== LEAVE INTEGRATION METHODS ==================== */

    /**
     * Check if employee is on approved leave for a given date
     */
    public static function isEmployeeOnLeave($employeeId, $date): bool
    {
        return Leave::where('employee_id', $employeeId)
            ->where('status', 'Approved')
            ->whereDate('from_date', '<=', $date)
            ->whereDate('to_date', '>=', $date)
            ->exists();
    }

    /**
     * Get leave record if employee is on leave for a specific date
     */
    public static function getLeaveForDate($employeeId, $date): ?Leave
    {
        return Leave::where('employee_id', $employeeId)
            ->where('status', 'Approved')
            ->whereDate('from_date', '<=', $date)
            ->whereDate('to_date', '>=', $date)
            ->first();
    }

    /**
     * Check if date is a holiday
     */
    public static function isHoliday($date): bool
    {
        return LeaveHoliday::where('date', $date)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if date is a weekend
     */
    public static function isWeekend($date): bool
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        return in_array($dayOfWeek, [0, 6]); // 0 = Sunday, 6 = Saturday
    }

    /**
     * Get holiday record for a specific date
     */
    public static function getHolidayForDate($date): ?LeaveHoliday
    {
        return LeaveHoliday::where('date', $date)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Check if attendance can be marked (not on leave, not holiday, not weekend)
     */
    public function canMarkAttendance(): bool
    {
        // Can't mark if already on leave
        if ($this->status === self::STATUS_ON_LEAVE) {
            return false;
        }

        // Check if on approved leave
        if (self::isEmployeeOnLeave($this->employee_id, $this->attendance_date)) {
            return false;
        }

        return true;
    }

    /**
     * Validate attendance marking against leave
     */
    public function validateAgainstLeave(): array
    {
        $errors = [];

        // Check if employee is on approved leave
        if (self::isEmployeeOnLeave($this->employee_id, $this->attendance_date)) {
            $leave = self::getLeaveForDate($this->employee_id, $this->attendance_date);
            $errors[] = "Employee is on approved {$leave->leave_type_label} leave. Cannot mark attendance.";
        }

        // Check for conflicting attendance records
        $existing = self::where('employee_id', $this->employee_id)
            ->where('attendance_date', $this->attendance_date)
            ->where('id', '!=', $this->id ?? 0)
            ->first();

        if ($existing) {
            $errors[] = "Attendance already marked for this employee on this date.";
        }

        return $errors;
    }

    /**
     * Auto-mark attendance as "On Leave" for approved leave dates
     */
    public static function autoMarkLeaveAttendance(Leave $leave): void
    {
        if (!$leave->isApproved()) {
            return;
        }

        foreach ($leave->getDateRange() as $date) {
            // Skip if attendance already exists
            if (self::where('employee_id', $leave->employee_id)
                ->where('attendance_date', $date)
                ->exists()) {
                continue;
            }

            // Skip weekends
            if (self::isWeekend($date)) {
                continue;
            }

            // Skip holidays
            if (self::isHoliday($date)) {
                continue;
            }

            self::create([
                'employee_id' => $leave->employee_id,
                'leave_id' => $leave->id,
                'attendance_date' => $date,
                'status' => self::STATUS_ON_LEAVE,
                'is_auto_marked' => true,
                'remarks' => "Auto-marked: {$leave->leave_type_label} leave (Ref: {$leave->leave_number})"
            ]);
        }
    }

    /**
     * Remove auto-marked leave attendance
     */
    public static function removeLeaveAttendance(Leave $leave): void
    {
        $leave->attendances()
            ->where('is_auto_marked', true)
            ->where('status', self::STATUS_ON_LEAVE)
            ->delete();
    }

    /**
     * Get leave summary for employee in a date range
     */
    public function getEmployeeLeavesSummary($fromDate, $toDate): array
    {
        $leaves = Leave::where('employee_id', $this->employee_id)
            ->where('status', 'Approved')
            ->whereBetween('from_date', [$fromDate, $toDate])
            ->get()
            ->groupBy('leave_type')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total_days' => $group->sum('total_days')
                ];
            });

        return $leaves->toArray();
    }

    /* ==================== HELPER METHODS ==================== */
}

