<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentTracking extends Model
{
    protected $fillable = [
        // Basic Tracking
        'shipment_id',
        'status',
        'location',
        'latitude',
        'longitude',
        'remarks',
        'updated_by',
        'tracked_at',

        // ✅ NEW FIELDS
        'city',
        'state',
        'country',
        'pincode',
        'accuracy',
        'speed',
        'heading',
        'altitude',
        'event_type',
        'is_public',
        'metadata'
    ];

    protected $casts = [
        // Coordinates
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',

        // Dates
        'tracked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',

        // Tracking details
        'accuracy' => 'integer',
        'speed' => 'decimal:2',
        'heading' => 'integer',
        'altitude' => 'decimal:2',

        // JSON
        'metadata' => 'array',

        // Booleans
        'is_public' => 'boolean',

        // Location strings
        'city' => 'string',
        'state' => 'string',
        'country' => 'string',
        'pincode' => 'string',
        'event_type' => 'string',
    ];

    /* ==================== EXISTING RELATIONSHIPS ==================== */

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /* ==================== NEW RELATIONSHIPS ==================== */

    /**
     * Get the shipment event associated with this tracking
     */
    public function event()
    {
        return $this->hasOne(ShipmentEvent::class, 'shipment_id', 'shipment_id')
                    ->where('occurred_at', $this->tracked_at);
    }

    /* ==================== EXISTING ACCESSORS ==================== */

    public function getFormattedTrackedAtAttribute()
    {
        return $this->tracked_at->format('d M Y - h:i A');
    }

    public function getStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'picked' => 'info',
            'in_transit' => 'primary',
            'out_for_delivery' => 'secondary',
            'delivered' => 'success',
            'failed' => 'danger',
            'returned' => 'dark',
            'cancelled' => 'secondary',
            'assigned' => 'info',
            'pod_uploaded' => 'success'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /* ==================== NEW ACCESSORS ==================== */

    /**
     * Get status with icon
     */
    public function getStatusWithIconAttribute()
    {
        $icons = [
            'pending' => '⏳ Pending',
            'picked' => '📦 Picked Up',
            'in_transit' => '🚚 In Transit',
            'out_for_delivery' => '🚀 Out for Delivery',
            'delivered' => '✅ Delivered',
            'failed' => '❌ Failed',
            'returned' => '🔄 Returned',
            'cancelled' => '❌ Cancelled',
            'assigned' => '👤 Assigned',
            'pod_uploaded' => '📸 POD Uploaded'
        ];

        return $icons[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Get full location string
     */
    public function getFullLocationAttribute()
    {
        $location = $this->location ?? '';

        if ($this->city) {
            $location .= ($location ? ', ' : '') . $this->city;
        }
        if ($this->state) {
            $location .= ($location ? ', ' : '') . $this->state;
        }
        if ($this->pincode) {
            $location .= ($location ? ' - ' : '') . $this->pincode;
        }
        if ($this->country && $this->country !== 'India') {
            $location .= ($location ? ', ' : '') . $this->country;
        }

        return $location ?: 'Location not specified';
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
     * Get Google Maps link
     */
    public function getGoogleMapsLinkAttribute()
    {
        if ($this->coordinates) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }

        return null;
    }

    /**
     * Get formatted accuracy
     */
    public function getFormattedAccuracyAttribute()
    {
        if (!$this->accuracy) {
            return null;
        }

        if ($this->accuracy < 10) {
            return "Excellent (±{$this->accuracy}m)";
        } elseif ($this->accuracy < 50) {
            return "Good (±{$this->accuracy}m)";
        } elseif ($this->accuracy < 100) {
            return "Fair (±{$this->accuracy}m)";
        } else {
            return "Poor (±{$this->accuracy}m)";
        }
    }

    /**
     * Get formatted speed
     */
    public function getFormattedSpeedAttribute()
    {
        return $this->speed ? $this->speed . ' km/h' : null;
    }

    /**
     * Get formatted altitude
     */
    public function getFormattedAltitudeAttribute()
    {
        return $this->altitude ? $this->altitude . ' m' : null;
    }

    /**
     * Get event type display
     */
    public function getEventTypeDisplayAttribute()
    {
        $events = [
            'status_updated' => 'Status Updated',
            'location_updated' => 'Location Updated',
            'agent_assigned' => 'Agent Assigned',
            'pod_uploaded' => 'POD Uploaded',
            'delivery_attempt' => 'Delivery Attempt',
            'customer_contacted' => 'Customer Contacted',
            'exception' => 'Exception',
            'return_initiated' => 'Return Initiated'
        ];

        return $events[$this->event_type] ?? ucfirst(str_replace('_', ' ', $this->event_type));
    }

    /**
     * Check if this tracking has location
     */
    public function getHasLocationAttribute()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Get time ago in human readable format
     */
    public function getTimeAgoAttribute()
    {
        return $this->tracked_at->diffForHumans();
    }

    /**
     * Get short time format
     */
    public function getShortTimeAttribute()
    {
        return $this->tracked_at->format('h:i A');
    }

    /**
     * Get short date format
     */
    public function getShortDateAttribute()
    {
        return $this->tracked_at->format('d M');
    }

    /**
     * Get full datetime for display
     */
    public function getDisplayDateTimeAttribute()
    {
        return $this->tracked_at->format('d M Y, h:i A');
    }

    /**
     * Get metadata value by key
     */
    public function getMetadataValue($key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * Get all metadata as array
     */
    public function getMetadataArrayAttribute()
    {
        return $this->metadata ?? [];
    }

    /* ==================== METHODS ==================== */

    /**
     * Check if tracking is public visible
     */
    public function isPublic()
    {
        return $this->is_public;
    }

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
     * Create a shipment event from this tracking
     */
    public function createEvent()
    {
        if (!class_exists(ShipmentEvent::class)) {
            return null;
        }

        return ShipmentEvent::create([
            'shipment_id' => $this->shipment_id,
            'event_type' => $this->event_type ?? 'tracking_updated',
            'status_from' => null,
            'status_to' => $this->status,
            'location' => $this->full_location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'description' => $this->remarks,
            'metadata' => [
                'tracking_id' => $this->id,
                'accuracy' => $this->accuracy,
                'speed' => $this->speed
            ],
            'triggered_by' => $this->updated_by ? 'user' : 'system',
            'triggered_by_id' => $this->updated_by,
            'occurred_at' => $this->tracked_at
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
     * Scope by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope by multiple statuses
     */
    public function scopeInStatuses($query, array $statuses)
    {
        return $query->whereIn('status', $statuses);
    }

    /**
     * Scope latest first
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('tracked_at', 'desc');
    }

    /**
     * Scope oldest first
     */
    public function scopeOldestFirst($query)
    {
        return $query->orderBy('tracked_at', 'asc');
    }

    /**
     * Scope trackings in date range
     */
    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('tracked_at', [$from, $to]);
    }

    /**
     * Scope trackings with location
     */
    public function scopeWithLocation($query)
    {
        return $query->whereNotNull('latitude')
                     ->whereNotNull('longitude');
    }

    /**
     * Scope public trackings
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope by city
     */
    public function scopeInCity($query, $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    /**
     * Scope by event type
     */
    public function scopeOfEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope recent trackings (last N hours)
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('tracked_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope trackings with accuracy better than
     */
    public function scopeAccurateBetterThan($query, $meters)
    {
        return $query->where('accuracy', '<=', $meters);
    }

    /* ==================== STATIC METHODS ==================== */

    /**
     * Get latest tracking for a shipment
     */
    public static function getLatestForShipment($shipmentId)
    {
        return self::forShipment($shipmentId)
                   ->latestFirst()
                   ->first();
    }

    /**
     * Get tracking timeline for a shipment
     */
    public static function getTimeline($shipmentId, $limit = 20)
    {
        return self::forShipment($shipmentId)
                   ->latestFirst()
                   ->limit($limit)
                   ->get();
    }

    /**
     * Create initial tracking for new shipment
     */
    public static function createInitial($shipment, $remarks = null)
    {
        $tracking = self::create([
            'shipment_id' => $shipment->id,
            'status' => $shipment->status,
            'location' => $shipment->city,
            'remarks' => $remarks ?? 'Shipment created',
            'updated_by' => auth()->id() ?? 'system',
            'tracked_at' => now(),
            'city' => $shipment->city,
            'state' => $shipment->state,
            'country' => $shipment->country,
            'is_public' => true,
            'event_type' => 'shipment_created'
        ]);

        return $tracking;
    }

    /**
     * Get statistics for dashboard
     */
    public static function getStats($shipmentId = null)
    {
        $query = self::query();

        if ($shipmentId) {
            $query->forShipment($shipmentId);
        }

        return [
            'total' => $query->count(),
            'with_location' => (clone $query)->withLocation()->count(),
            'public' => (clone $query)->public()->count(),
            'today' => (clone $query)->whereDate('tracked_at', today())->count(),
            'latest' => (clone $query)->latestFirst()->limit(5)->get()
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tracking) {
            if (empty($tracking->tracked_at)) {
                $tracking->tracked_at = now();
            }

            if (empty($tracking->is_public)) {
                $tracking->is_public = true;
            }

            if (empty($tracking->country)) {
                $tracking->country = 'India';
            }
        });

        static::created(function ($tracking) {
            // Update shipment's last tracking info
            if ($tracking->shipment) {
                $tracking->shipment->last_location_update = $tracking->tracked_at;
                $tracking->shipment->save();
            }

            // Create shipment event
            $tracking->createEvent();
        });
    }
}
