<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentEvent extends Model
{
    protected $table = 'shipment_events';

    protected $fillable = [
        'shipment_id',
        'event_type',
        'status_from',
        'status_to',
        'location',
        'latitude',
        'longitude',
        'description',
        'metadata',
        'triggered_by',
        'triggered_by_id',
        'occurred_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'occurred_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the shipment for this event
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * Get the user who triggered this event
     */
    public function trigger()
    {
        return $this->belongsTo(User::class, 'triggered_by_id');
    }

    /**
     * Get the associated tracking record (if any)
     */
    public function tracking()
    {
        return $this->hasOne(ShipmentTracking::class, 'shipment_id', 'shipment_id')
                    ->where('tracked_at', $this->occurred_at);
    }

    /* ==================== ACCESSORS ==================== */

    /**
     * Get event type with icon
     */
    public function getEventTypeDisplayAttribute()
    {
        $icons = [
            'shipment_created' => '📦 Created',
            'status_changed' => '🔄 Status Changed',
            'agent_assigned' => '👤 Agent Assigned',
            'agent_changed' => '👤 Agent Changed',
            'location_updated' => '📍 Location Updated',
            'delivery_attempt' => '🚚 Delivery Attempt',
            'delivered' => '✅ Delivered',
            'failed' => '❌ Failed',
            'returned' => '🔄 Returned',
            'cancelled' => '❌ Cancelled',
            'pod_uploaded' => '📸 POD Uploaded',
            'otp_verified' => '🔐 OTP Verified',
            'exception' => '⚠️ Exception'
        ];

        return $icons[$this->event_type] ?? ucfirst(str_replace('_', ' ', $this->event_type));
    }

    /**
     * Get status from display
     */
    public function getStatusFromDisplayAttribute()
    {
        return $this->status_from ? ucfirst(str_replace('_', ' ', $this->status_from)) : null;
    }

    /**
     * Get status to display
     */
    public function getStatusToDisplayAttribute()
    {
        return $this->status_to ? ucfirst(str_replace('_', ' ', $this->status_to)) : null;
    }

    /**
     * Get coordinates as array
     */
    public function getCoordinatesAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => $this->latitude,
                'lng' => $this->longitude
            ];
        }
        return null;
    }

    /**
     * Get formatted occurred time
     */
    public function getFormattedOccurredAtAttribute()
    {
        return $this->occurred_at->format('d M Y, h:i A');
    }

    /**
     * Get time ago
     */
    public function getTimeAgoAttribute()
    {
        return $this->occurred_at->diffForHumans();
    }

    /**
     * Get trigger type display
     */
    public function getTriggerDisplayAttribute()
    {
        $types = [
            'system' => '⚙️ System',
            'user' => '👤 User',
            'agent' => '🛵 Agent',
            'courier_api' => '📡 Courier API'
        ];

        return $types[$this->triggered_by] ?? $this->triggered_by;
    }

    /**
     * Get metadata value by key
     */
    public function getMetadataValue($key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * Get summary of event
     */
    public function getSummaryAttribute()
    {
        $summary = $this->event_type_display;

        if ($this->status_from && $this->status_to) {
            $summary .= ": {$this->status_from_display} → {$this->status_to_display}";
        }

        if ($this->location) {
            $summary .= " at {$this->location}";
        }

        return $summary;
    }

    /* ==================== METHODS ==================== */

    /**
     * Set metadata value
     */
    public function setMetadata($key, $value)
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Add multiple metadata values
     */
    public function addMetadata(array $data)
    {
        $metadata = $this->metadata ?? [];
        $this->metadata = array_merge($metadata, $data);

        return $this;
    }

    /**
     * Create event from tracking
     */
    public static function createFromTracking(ShipmentTracking $tracking)
    {
        return self::create([
            'shipment_id' => $tracking->shipment_id,
            'event_type' => 'status_changed',
            'status_from' => null,
            'status_to' => $tracking->status,
            'location' => $tracking->full_location,
            'latitude' => $tracking->latitude,
            'longitude' => $tracking->longitude,
            'description' => $tracking->remarks,
            'metadata' => [
                'tracking_id' => $tracking->id,
                'accuracy' => $tracking->accuracy,
                'speed' => $tracking->speed
            ],
            'triggered_by' => $tracking->updated_by ? 'user' : 'system',
            'triggered_by_id' => $tracking->updated_by,
            'occurred_at' => $tracking->tracked_at
        ]);
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope by shipment
     */
    public function scopeForShipment($query, $shipmentId)
    {
        return $query->where('shipment_id', $shipmentId);
    }

    /**
     * Scope by event type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope by trigger
     */
    public function scopeTriggeredBy($query, $trigger)
    {
        return $query->where('triggered_by', $trigger);
    }

    /**
     * Scope between dates
     */
    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('occurred_at', [$from, $to]);
    }

    /**
     * Scope recent events
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('occurred_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope latest first
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('occurred_at', 'desc');
    }

    /**
     * Scope oldest first
     */
    public function scopeOldestFirst($query)
    {
        return $query->orderBy('occurred_at', 'asc');
    }

    /* ==================== STATIC METHODS ==================== */

    /**
     * Get timeline for shipment
     */
    public static function getTimeline($shipmentId, $limit = 50)
    {
        return self::forShipment($shipmentId)
            ->latestFirst()
            ->limit($limit)
            ->get();
    }

    /**
     * Get events summary
     */
    public static function getSummary($shipmentId)
    {
        $events = self::forShipment($shipmentId)->get();

        return [
            'total' => $events->count(),
            'by_type' => $events->groupBy('event_type')->map->count(),
            'by_trigger' => $events->groupBy('triggered_by')->map->count(),
            'first_event' => $events->sortBy('occurred_at')->first(),
            'last_event' => $events->sortByDesc('occurred_at')->first()
        ];
    }

    /* ==================== BOOT METHOD ==================== */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->occurred_at)) {
                $event->occurred_at = now();
            }
        });
    }
}
