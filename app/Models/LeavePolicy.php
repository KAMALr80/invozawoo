<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeavePolicy extends Model
{
    use HasFactory;

    protected $table = 'leave_policies';

    protected $fillable = [
        'name',
        'leave_type',
        'days_per_year',
        'accrual_method',
        'carry_forward_allowed',
        'max_carry_forward_days',
        'min_service_days',
        'applicable_gender',
        'is_paid',
        'max_consecutive_days',
        'min_notice_days',
        'requires_approval',
        'requires_document',
        'requires_handover',
        'is_active',
        'effective_from',
        'effective_to',
        'description'
    ];

    protected $casts = [
        'carry_forward_allowed' => 'boolean',
        'is_paid' => 'boolean',
        'requires_approval' => 'boolean',
        'requires_document' => 'boolean',
        'requires_handover' => 'boolean',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date'
    ];

    /* ==================== ACCESSORS ==================== */

    public function getLeaveTypeLabelAttribute(): string
    {
        $labels = [
            'annual' => 'Annual Leave',
            'sick' => 'Sick Leave',
            'casual' => 'Casual Leave',
            'unpaid' => 'Unpaid Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave',
            'bereavement' => 'Bereavement Leave',
            'study' => 'Study Leave'
        ];

        return $labels[$this->leave_type] ?? ucfirst(str_replace('_', ' ', $this->leave_type));
    }

    public function getAccrualMethodLabelAttribute(): string
    {
        $methods = [
            'lump_sum' => 'Lump Sum (Yearly)',
            'monthly' => 'Monthly Accrual',
            'bi_weekly' => 'Bi-Weekly Accrual',
            'weekly' => 'Weekly Accrual'
        ];

        return $methods[$this->accrual_method] ?? ucfirst($this->accrual_method);
    }
}
