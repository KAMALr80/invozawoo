<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Leave extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'leave_number',
        'leave_type',
        'duration_type',
        'from_date',
        'to_date',
        'total_days',
        'session',
        'start_time',
        'end_time',
        'reason',
        'contact_number',
        'handover_notes',
        'document_path',
        'status',
        'approved_by',
        'approved_at',
        'approval_remarks',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'cancelled_by',
        'cancelled_at',
        'leave_balance_before',
        'leave_balance_after',
        'applied_on'
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'applied_on' => 'datetime',
        'total_days' => 'decimal:2',
        'leave_balance_before' => 'decimal:2',
        'leave_balance_after' => 'decimal:2'
    ];

    protected $attributes = [
        'status' => 'Pending',
        'duration_type' => 'full_day'
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the employee who requested leave
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who approved this leave
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who rejected this leave
     */
    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the user who cancelled this leave
     */
    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Get all attendance records created for this leave
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the leave policy for this leave type
     */
    public function policy()
    {
        return $this->belongsTo(LeavePolicy::class, 'leave_type', 'leave_type');
    }

    /**
     * Get leave balance record for this employee and leave type
     */
    public function leaveBalance()
    {
        return $this->employee->leaveBalances()
            ->where('leave_type', $this->leave_type)
            ->where('year', now()->year);
    }

    /* ==================== ACCESSORS ==================== */

    public function getFormattedFromDateAttribute()
    {
        return Carbon::parse($this->from_date)->format('d M, Y');
    }

    public function getFormattedToDateAttribute()
    {
        return Carbon::parse($this->to_date)->format('d M, Y');
    }

    public function getLeaveTypeLabelAttribute()
    {
        $labels = [
            'annual' => 'Annual Leave',
            'sick' => 'Sick Leave',
            'casual' => 'Casual Leave',
            'unpaid' => 'Unpaid Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave',
            'bereavement' => 'Bereavement Leave',
            'study' => 'Study Leave',
            'half_day' => 'Half Day',
            'short_leave' => 'Short Leave'
        ];
        return $labels[$this->leave_type] ?? ucfirst(str_replace('_', ' ', $this->leave_type));
    }

    public function getDurationLabelAttribute()
    {
        $labels = [
            'full_day' => 'Full Day',
            'half_day' => 'Half Day',
            'short_leave' => 'Short Leave'
        ];
        return $labels[$this->duration_type] ?? ucfirst(str_replace('_', ' ', $this->duration_type));
    }

    /* ==================== STATUS CHECKS ==================== */

    public function isPending()
    {
        return $this->status === 'Pending';
    }

    public function isApproved()
    {
        return $this->status === 'Approved';
    }

    public function isRejected()
    {
        return $this->status === 'Rejected';
    }

    public function isCancelled()
    {
        return $this->status === 'Cancelled';
    }

    /**
     * Check if leave is within working days (excluding weekends and holidays)
     */
    public function getWorkingDaysAttribute()
    {
        if (!$this->from_date || !$this->to_date) {
            return 0;
        }

        $working = 0;
        $current = Carbon::parse($this->from_date);
        $end = Carbon::parse($this->to_date);

        while ($current <= $end) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if (!in_array($current->dayOfWeek, [0, 6])) {
                // Check if not a holiday
                $isHoliday = LeaveHoliday::where('date', $current->toDateString())
                    ->where('is_active', true)
                    ->exists();

                if (!$isHoliday) {
                    $working++;
                }
            }
            $current->addDay();
        }

        return $working;
    }

    /**
     * Get dates range for this leave
     */
    public function getDateRange()
    {
        $dates = [];
        $current = Carbon::parse($this->from_date);
        $end = Carbon::parse($this->to_date);

        while ($current <= $end) {
            $dates[] = $current->toDateString();
            $current->addDay();
        }

        return $dates;
    }

    /**
     * Check if employee has sufficient leave balance
     */
    public function hasSufficientBalance(): bool
    {
        $balance = $this->employee->leaveBalances()
            ->where('leave_type', $this->leave_type)
            ->where('year', now()->year)
            ->first();

        if (!$balance) {
            return false;
        }

        return $balance->total_available >= $this->total_days;
    }

    /**
     * Check for leave conflicts in the date range
     */
    public function hasConflicts(): bool
    {
        return self::where('employee_id', $this->employee_id)
            ->where('id', '!=', $this->id)
            ->where('status', 'Approved')
            ->whereBetween('from_date', [$this->from_date, $this->to_date])
            ->exists() || self::where('employee_id', $this->employee_id)
            ->where('id', '!=', $this->id)
            ->where('status', 'Approved')
            ->whereBetween('to_date', [$this->from_date, $this->to_date])
            ->exists();
    }

    /**
     * Check if leave overlaps with specific date
     */
    public function overlapsDate($date): bool
    {
        $date = Carbon::parse($date);
        return $date->between(
            Carbon::parse($this->from_date),
            Carbon::parse($this->to_date)
        );
    }

    /* ==================== BUSINESS LOGIC METHODS ==================== */

    /**
     * Create attendance records for approved leave
     */
    public function createLeaveAttendanceRecords(): void
    {
        if (!$this->isApproved()) {
            return;
        }

        foreach ($this->getDateRange() as $date) {
            // Check if attendance already exists for this date
            $existing = Attendance::where('employee_id', $this->employee_id)
                ->where('attendance_date', $date)
                ->first();

            if (!$existing) {
                Attendance::create([
                    'employee_id' => $this->employee_id,
                    'leave_id' => $this->id,
                    'attendance_date' => $date,
                    'status' => 'On Leave',
                    'is_auto_marked' => true,
                    'remarks' => "Auto-marked for {$this->leave_type_label} leave"
                ]);
            }
        }
    }

    /**
     * Delete attendance records when leave is cancelled or rejected
     */
    public function deleteLeaveAttendanceRecords(): void
    {
        $this->attendances()->delete();
    }

    /**
     * Update leave balance after approval
     */
    public function updateLeaveBalance(): void
    {
        $balance = $this->employee->leaveBalances()
            ->where('leave_type', $this->leave_type)
            ->where('year', now()->year)
            ->first();

        if ($balance) {
            $balance->used += $this->total_days;
            $balance->remaining = $balance->total_available - $balance->used - $balance->pending;
            $balance->save();

            $this->leave_balance_before = $balance->remaining + $this->total_days;
            $this->leave_balance_after = $balance->remaining;
            $this->save();
        }
    }

    /**
     * Reverse leave balance when leave is rejected or cancelled
     */
    public function reverseLeaveBalance(): void
    {
        $balance = $this->employee->leaveBalances()
            ->where('leave_type', $this->leave_type)
            ->where('year', now()->year)
            ->first();

        if ($balance && $this->status === 'Approved') {
            $balance->used -= $this->total_days;
            $balance->remaining = $balance->total_available - $balance->used - $balance->pending;
            $balance->save();
        }
    }

    /* ==================== SCOPES ==================== */

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForYear($query, $year)
    {
        return $query->whereYear('from_date', $year);
    }

    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('from_date', [$from, $to]);
    }
}
