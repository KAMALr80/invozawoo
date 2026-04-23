<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $table = 'leave_balances';

    protected $fillable = [
        'employee_id',
        'year',
        'leave_type',
        'entitled',
        'used',
        'remaining',
        'pending',
        'carry_forward',
        'total_available',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'entitled' => 'decimal:2',
        'used' => 'decimal:2',
        'remaining' => 'decimal:2',
        'pending' => 'decimal:2',
        'carry_forward' => 'decimal:2',
        'total_available' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the employee that owns this leave balance
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /* ==================== ACCESSORS ==================== */

    public function getLeaveTypeLabelAttribute(): string
    {
        $labels = [
            'annual' => 'Annual Leave',
            'sick' => 'Sick Leave',
            'casual' => 'Casual Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave',
            'bereavement' => 'Bereavement Leave',
            'study' => 'Study Leave'
        ];

        return $labels[$this->leave_type] ?? ucfirst(str_replace('_', ' ', $this->leave_type));
    }

    /* ==================== METHODS ==================== */

    public function calculateTotals(): void
    {
        $this->total_available = $this->entitled + $this->carry_forward;
        $this->remaining = $this->total_available - $this->used - $this->pending;
        $this->save();
    }
}
