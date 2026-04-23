<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteHistory extends Model
{
    protected $table = 'route_history';

    protected $fillable = [
        'route_id',
        'agent_id',
        'shipment_id',
        'stop_order',
        'arrival_lat',
        'arrival_lng',
        'arrived_at',
        'departed_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'arrival_lat' => 'decimal:8',
        'arrival_lng' => 'decimal:8',
        'arrived_at' => 'datetime',
        'departed_at' => 'datetime',
        'stop_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the route for this history
     */
    public function route()
    {
        return $this->belongsTo(SavedRoute::class, 'route_id');
    }

    /**
     * Get the agent for this history
     */
    public function agent()
    {
        return $this->belongsTo(DeliveryAgent::class);
    }

    /**
     * Get the shipment for this stop
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    /* ==================== ACCESSORS ==================== */

    /**
     * Get arrival coordinates as array
     */
    public function getArrivalCoordinatesAttribute()
    {
        if ($this->arrival_lat && $this->arrival_lng) {
            return [
                'lat' => $this->arrival_lat,
                'lng' => $this->arrival_lng
            ];
        }
        return null;
    }

    /**
     * Get formatted arrived time
     */
    public function getFormattedArrivedAtAttribute()
    {
        return $this->arrived_at?->format('h:i A');
    }

    /**
     * Get formatted departed time
     */
    public function getFormattedDepartedAtAttribute()
    {
        return $this->departed_at?->format('h:i A');
    }

    /**
     * Get duration at stop
     */
    public function getDurationMinutesAttribute()
    {
        if ($this->arrived_at && $this->departed_at) {
            return $this->arrived_at->diffInMinutes($this->departed_at);
        }
        return null;
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        $minutes = $this->duration_minutes;

        if (!$minutes) {
            return 'N/A';
        }

        if ($minutes < 60) {
            return $minutes . ' min';
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return $hours . 'h ' . $mins . 'm';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'arrived' => 'info',
            'completed' => 'success',
            'skipped' => 'secondary'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get status display
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'pending' => '⏳ Pending',
            'arrived' => '📍 Arrived',
            'completed' => '✅ Completed',
            'skipped' => '⏭️ Skipped'
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Check if stop is completed
     */
    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed' && !is_null($this->departed_at);
    }

    /**
     * Get receiver name from shipment
     */
    public function getReceiverNameAttribute()
    {
        return $this->shipment?->receiver_name;
    }

    /**
     * Get address from shipment
     */
    public function getAddressAttribute()
    {
        return $this->shipment?->full_address;
    }

    /* ==================== METHODS ==================== */

    /**
     * Mark as arrived
     */
    public function markAsArrived($latitude = null, $longitude = null)
    {
        $this->status = 'arrived';
        $this->arrived_at = now();

        if ($latitude && $longitude) {
            $this->arrival_lat = $latitude;
            $this->arrival_lng = $longitude;
        }

        $this->save();

        return $this;
    }

    /**
     * Mark as completed (departed)
     */
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->departed_at = now();
        $this->save();

        return $this;
    }

    /**
     * Mark as skipped
     */
    public function markAsSkipped($notes = null)
    {
        $this->status = 'skipped';
        $this->notes = $notes;
        $this->save();

        return $this;
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope by route
     */
    public function scopeForRoute($query, $routeId)
    {
        return $query->where('route_id', $routeId);
    }

    /**
     * Scope by agent
     */
    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pending stops
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope completed stops
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope by date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('arrived_at', $date);
    }

    /**
     * Scope order by stop order
     */
    public function scopeInOrder($query)
    {
        return $query->orderBy('stop_order', 'asc');
    }

    /* ==================== STATIC METHODS ==================== */

    /**
     * Create from route
     */
    public static function createFromRoute(SavedRoute $route, $agentId = null)
    {
        $histories = [];
        $shipments = $route->shipments();
        $optimizedOrder = $route->optimized_order ?? $route->shipment_ids ?? [];

        foreach ($optimizedOrder as $index => $shipmentId) {
            $shipment = $shipments->firstWhere('id', $shipmentId);

            if ($shipment) {
                $histories[] = self::create([
                    'route_id' => $route->id,
                    'agent_id' => $agentId ?? $route->agent_id,
                    'shipment_id' => $shipmentId,
                    'stop_order' => $index + 1,
                    'status' => 'pending'
                ]);
            }
        }

        return collect($histories);
    }

    /**
     * Get agent's route progress
     */
    public static function getAgentProgress($agentId, $routeId = null)
    {
        $query = self::forAgent($agentId);

        if ($routeId) {
            $query->forRoute($routeId);
        }

        $total = $query->count();
        $completed = (clone $query)->completed()->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $total - $completed,
            'progress' => $total > 0 ? round(($completed / $total) * 100, 2) : 0
        ];
    }
}
