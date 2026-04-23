<?php
// app/Models/AgentPerformanceLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentPerformanceLog extends Model
{
    protected $table = 'agent_performance_logs';

    protected $fillable = [
        'agent_id',        // ✅ This is correct column name
        'log_date',
        'shipments_assigned',
        'shipments_delivered',
        'shipments_failed',
        'total_distance_km',
        'total_time_minutes',
        'average_rating',
        'first_active_at',
        'last_active_at',
        'active_minutes',
        'base_pay',
        'commission_earned',
        'bonus_earned',
        'total_earnings'
    ];

    protected $casts = [
        'log_date' => 'date',
        'shipments_assigned' => 'integer',
        'shipments_delivered' => 'integer',
        'shipments_failed' => 'integer',
        'total_distance_km' => 'decimal:2',
        'total_time_minutes' => 'integer',
        'average_rating' => 'decimal:2',
        'first_active_at' => 'datetime',
        'last_active_at' => 'datetime',
        'active_minutes' => 'integer',
        'base_pay' => 'decimal:2',
        'commission_earned' => 'decimal:2',
        'bonus_earned' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the agent for this log
     */
    public function agent()
    {
        return $this->belongsTo(DeliveryAgent::class, 'agent_id'); // ✅ Foreign key is 'agent_id'
    }

    /* ==================== ACCESSORS ==================== */

    /**
     * Get success rate for the day
     */
    public function getSuccessRateAttribute()
    {
        if ($this->shipments_assigned > 0) {
            return round(($this->shipments_delivered / $this->shipments_assigned) * 100, 2);
        }
        return 0;
    }

    /**
     * Get formatted success rate
     */
    public function getFormattedSuccessRateAttribute()
    {
        return $this->success_rate . '%';
    }

    /**
     * Get formatted distance
     */
    public function getFormattedDistanceAttribute()
    {
        return $this->total_distance_km ? $this->total_distance_km . ' km' : '0 km';
    }

    /**
     * Get formatted time
     */
    public function getFormattedTimeAttribute()
    {
        $hours = floor($this->total_time_minutes / 60);
        $minutes = $this->total_time_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . 'm';
    }

    /**
     * Get formatted active time
     */
    public function getFormattedActiveTimeAttribute()
    {
        $hours = floor($this->active_minutes / 60);
        $minutes = $this->active_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . 'm';
    }

    /**
     * Get formatted total earnings
     */
    public function getFormattedTotalEarningsAttribute()
    {
        return '₹ ' . number_format($this->total_earnings, 2);
    }

    /**
     * Get performance score (0-100)
     */
    public function getPerformanceScoreAttribute()
    {
        $score = 0;

        // Success rate (50% weight)
        $score += $this->success_rate * 0.5;

        // Deliveries count (30% weight, max 15 deliveries)
        $deliveryScore = min($this->shipments_delivered, 15) / 15 * 30;
        $score += $deliveryScore;

        // Rating (20% weight)
        $score += ($this->average_rating / 5) * 20;

        return round($score, 2);
    }

    /* ==================== METHODS ==================== */

    /**
     * Calculate total earnings
     */
    public function calculateTotalEarnings()
    {
        $this->total_earnings = $this->base_pay + $this->commission_earned + $this->bonus_earned;
        return $this->total_earnings;
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope by agent
     */
    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);  // ✅ Correct column name
    }

    /**
     * Scope by date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('log_date', $date);
    }

    /**
     * Scope between dates
     */
    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('log_date', [$from, $to]);
    }

    /**
     * Scope this month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('log_date', now()->month)
                     ->whereYear('log_date', now()->year);
    }

    /**
     * Scope top performers
     */
    public function scopeTopPerformers($query, $limit = 10)
    {
        return $query->where('shipments_delivered', '>', 0)
                     ->orderBy('shipments_delivered', 'desc')
                     ->orderBy('success_rate', 'desc')
                     ->limit($limit);
    }

    /* ==================== STATIC METHODS ==================== */

    /**
     * Get or create log for today
     */
    public static function getTodayLog($agentId)
    {
        return self::firstOrCreate(
            [
                'agent_id' => $agentId,
                'log_date' => today()
            ],
            [
                'shipments_assigned' => 0,
                'shipments_delivered' => 0,
                'shipments_failed' => 0,
                'total_distance_km' => 0,
                'total_time_minutes' => 0,
                'active_minutes' => 0,
                'base_pay' => 0,
                'commission_earned' => 0,
                'bonus_earned' => 0,
                'total_earnings' => 0
            ]
        );
    }

    /**
     * Get agent performance summary
     */
    public static function getAgentSummary($agentId, $days = 30)
    {
        $logs = self::forAgent($agentId)
            ->betweenDates(now()->subDays($days), now())
            ->get();

        return [
            'total_days' => $logs->count(),
            'total_assigned' => $logs->sum('shipments_assigned'),
            'total_delivered' => $logs->sum('shipments_delivered'),
            'total_failed' => $logs->sum('shipments_failed'),
            'avg_success_rate' => $logs->avg('success_rate'),
            'total_distance' => $logs->sum('total_distance_km'),
            'total_active_minutes' => $logs->sum('active_minutes'),
            'total_earnings' => $logs->sum('total_earnings'),
            'avg_performance_score' => $logs->avg('performance_score')
        ];
    }

    /* ==================== BOOT METHOD ==================== */

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($log) {
            $log->calculateTotalEarnings();
        });

        static::created(function ($log) {
            // Update agent's performance metrics
            if ($log->agent) {
                $agent = $log->agent;
                $agent->avg_delivery_time = $log->agent->performanceLogs()
                    ->where('shipments_delivered', '>', 0)
                    ->avg('total_time_minutes');
                $agent->on_time_delivery_rate = $log->agent->performanceLogs()
                    ->avg('success_rate');
                $agent->save();
            }
        });
    }
}

