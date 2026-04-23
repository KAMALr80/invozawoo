<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SavedRoute extends Model
{
    use SoftDeletes;

    protected $table = 'saved_routes';

    protected $fillable = [
        'route_code',
        'name',
        'agent_id',
        'route_date',
        'waypoints',
        'shipment_ids',
        'total_distance',
        'total_duration',
        'optimized_order',
        'polyline',
        'start_lat',
        'start_lng',
        'start_address',
        'end_lat',
        'end_lng',
        'end_address',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'waypoints' => 'array',
        'shipment_ids' => 'array',
        'optimized_order' => 'array',
        'route_date' => 'date',
        'total_distance' => 'decimal:2',
        'total_duration' => 'integer',
        'start_lat' => 'decimal:8',
        'start_lng' => 'decimal:8',
        'end_lat' => 'decimal:8',
        'end_lng' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the agent assigned to this route
     */
    public function agent()
    {
        return $this->belongsTo(DeliveryAgent::class, 'agent_id');
    }

    /**
     * Get the user who created this route
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this route
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get route history for this route
     */
    public function routeHistory()
    {
        return $this->hasMany(RouteHistory::class);
    }

    /**
     * Get shipments for this route
     */
    public function shipments()
    {
        return Shipment::whereIn('id', $this->shipment_ids ?? [])->get();
    }

    /* ==================== ACCESSORS ==================== */

    /**
     * Get route code with name
     */
    public function getDisplayNameAttribute()
    {
        return $this->route_code . ' - ' . $this->name;
    }

    /**
     * Get formatted total distance
     */
    public function getFormattedDistanceAttribute()
    {
        return $this->total_distance ? $this->total_distance . ' km' : 'N/A';
    }

    /**
     * Get formatted total duration
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->total_duration) {
            return 'N/A';
        }

        $hours = floor($this->total_duration / 60);
        $minutes = $this->total_duration % 60;

        if ($hours > 0) {
            return $hours . ' hr ' . $minutes . ' min';
        }

        return $minutes . ' min';
    }

    /**
     * Get start coordinates as array
     */
    public function getStartCoordinatesAttribute()
    {
        if ($this->start_lat && $this->start_lng) {
            return [
                'lat' => $this->start_lat,
                'lng' => $this->start_lng
            ];
        }
        return null;
    }

    /**
     * Get end coordinates as array
     */
    public function getEndCoordinatesAttribute()
    {
        if ($this->end_lat && $this->end_lng) {
            return [
                'lat' => $this->end_lat,
                'lng' => $this->end_lng
            ];
        }
        return null;
    }

    /**
     * Get waypoints count
     */
    public function getWaypointsCountAttribute()
    {
        return count($this->waypoints ?? []);
    }

    /**
     * Get shipments count
     */
    public function getShipmentsCountAttribute()
    {
        return count($this->shipment_ids ?? []);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'draft' => 'secondary',
            'assigned' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get status display
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'draft' => '📝 Draft',
            'assigned' => '👤 Assigned',
            'completed' => '✅ Completed',
            'cancelled' => '❌ Cancelled'
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Check if route is assigned
     */
    public function getIsAssignedAttribute()
    {
        return $this->status === 'assigned' && !is_null($this->agent_id);
    }

    /**
     * Check if route is completed
     */
    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    /* ==================== METHODS ==================== */

    /**
     * Assign route to agent
     */
    public function assignToAgent($agentId)
    {
        $this->agent_id = $agentId;
        $this->status = 'assigned';
        $this->save();

        return $this;
    }

    /**
     * Mark route as completed
     */
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->save();

        return $this;
    }

    /**
     * Cancel route
     */
    public function cancel()
    {
        $this->status = 'cancelled';
        $this->save();

        return $this;
    }

    /**
     * Get optimized order with shipment details
     */
    public function getOptimizedOrderWithDetails()
    {
        $order = $this->optimized_order ?? [];
        $shipments = $this->shipments();

        return collect($order)->map(function($shipmentId) use ($shipments) {
            $shipment = $shipments->firstWhere('id', $shipmentId);
            return [
                'id' => $shipmentId,
                'shipment_number' => $shipment?->shipment_number,
                'receiver_name' => $shipment?->receiver_name,
                'address' => $shipment?->full_address,
                'coordinates' => $shipment?->coordinates
            ];
        })->toArray();
    }

    /**
     * Generate unique route code
     */
    public static function generateRouteCode()
    {
        $prefix = 'RTE';
        $date = now()->format('Ymd');
        $lastRoute = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRoute) {
            $lastCode = substr($lastRoute->route_code, -4);
            $sequence = intval($lastCode) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get dashboard statistics
     */
    public static function getDashboardStats()
    {
        return [
            'total' => self::count(),
            'draft' => self::where('status', 'draft')->count(),
            'assigned' => self::where('status', 'assigned')->count(),
            'completed' => self::where('status', 'completed')->count(),
            'cancelled' => self::where('status', 'cancelled')->count(),
            'total_distance' => self::sum('total_distance'),
            'avg_distance' => self::avg('total_distance')
        ];
    }

    /* ==================== SCOPES ==================== */

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
     * Scope by date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('route_date', $date);
    }

    /**
     * Scope active routes
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['draft', 'assigned']);
    }

    /**
     * Scope completed routes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope routes for today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('route_date', today());
    }

    /* ==================== BOOT METHOD ==================== */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($route) {
            if (empty($route->route_code)) {
                $route->route_code = self::generateRouteCode();
            }

            if (empty($route->status)) {
                $route->status = 'draft';
            }

            if (empty($route->route_date)) {
                $route->route_date = now();
            }
        });
    }
}

