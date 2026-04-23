<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeaveHoliday extends Model
{
    use HasFactory;

    protected $table = 'leave_holidays';

    protected $fillable = [
        'name',
        'date',
        'type',
        'repeats_annually',
        'applicable_to',
        'description',
        'is_active'
    ];

    protected $casts = [
        'date' => 'date',
        'repeats_annually' => 'boolean',
        'is_active' => 'boolean'
    ];

    /* ==================== ACCESSORS ==================== */

    public function getTypeLabelAttribute(): string
    {
        $types = [
            'public' => 'Public Holiday',
            'company' => 'Company Holiday',
            'restricted' => 'Restricted Holiday'
        ];

        return $types[$this->type] ?? ucfirst($this->type);
    }

    public function getFormattedDateAttribute(): string
    {
        return Carbon::parse($this->date)->format('d M, Y');
    }

    public function getDayNameAttribute(): string
    {
        return Carbon::parse($this->date)->format('l');
    }

    /* ==================== SCOPES ==================== */

    public function scopeUpcoming($query, $limit = 10)
    {
        return $query->where('date', '>=', now())
            ->where('is_active', true)
            ->orderBy('date')
            ->limit($limit);
    }

    public function scopeForYear($query, $year)
    {
        return $query->whereYear('date', $year);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
