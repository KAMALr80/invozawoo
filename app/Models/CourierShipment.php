<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourierShipment extends Model
{
    protected $table = 'courier_shipments';

    protected $fillable = [
        'shipment_id',
        'courier_partner_id',
        'courier_tracking_number',
        'courier_awb_number',
        'label_url',
        'manifest_url',
        'invoice_url',
        'courier_charge',
        'fuel_surcharge',
        'cod_charge',
        'total_courier_charge',
        'pickup_scheduled_at',
        'pickup_actual_at',
        'pickup_status',
        'delivery_estimated_at',
        'delivery_actual_at',
        'delivery_status',
        'api_request',
        'api_response',
        'status'
    ];

    protected $casts = [
        'api_request' => 'array',
        'api_response' => 'array',
        'courier_charge' => 'decimal:2',
        'fuel_surcharge' => 'decimal:2',
        'cod_charge' => 'decimal:2',
        'total_courier_charge' => 'decimal:2',
        'pickup_scheduled_at' => 'datetime',
        'pickup_actual_at' => 'datetime',
        'delivery_estimated_at' => 'datetime',
        'delivery_actual_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the shipment for this courier shipment
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * Get the courier partner for this shipment
     */
    public function courierPartner()
    {
        return $this->belongsTo(CourierPartner::class);
    }

    /* ==================== ACCESSORS ==================== */

    /**
     * Get formatted courier charge
     */
    public function getFormattedCourierChargeAttribute()
    {
        return '₹ ' . number_format($this->courier_charge, 2);
    }

    /**
     * Get formatted total charge
     */
    public function getFormattedTotalChargeAttribute()
    {
        return '₹ ' . number_format($this->total_courier_charge, 2);
    }

    /**
     * Get formatted pickup scheduled time
     */
    public function getFormattedPickupScheduledAttribute()
    {
        return $this->pickup_scheduled_at?->format('d M Y, h:i A');
    }

    /**
     * Get formatted pickup actual time
     */
    public function getFormattedPickupActualAttribute()
    {
        return $this->pickup_actual_at?->format('d M Y, h:i A');
    }

    /**
     * Get formatted delivery estimated time
     */
    public function getFormattedDeliveryEstimatedAttribute()
    {
        return $this->delivery_estimated_at?->format('d M Y, h:i A');
    }

    /**
     * Get formatted delivery actual time
     */
    public function getFormattedDeliveryActualAttribute()
    {
        return $this->delivery_actual_at?->format('d M Y, h:i A');
    }

    /**
     * Get label URL
     */
    public function getLabelUrlAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }

    /**
     * Get manifest URL
     */
    public function getManifestUrlAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }

    /**
     * Get invoice URL
     */
    public function getInvoiceUrlAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }

    /**
     * Get status badge color
     */
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
            'cancelled' => 'secondary'
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
            'picked' => '📦 Picked Up',
            'in_transit' => '🚚 In Transit',
            'out_for_delivery' => '🚀 Out for Delivery',
            'delivered' => '✅ Delivered',
            'failed' => '❌ Failed',
            'returned' => '🔄 Returned',
            'cancelled' => '❌ Cancelled'
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get pickup status display
     */
    public function getPickupStatusDisplayAttribute()
    {
        $statuses = [
            'scheduled' => '📅 Scheduled',
            'picked' => '✅ Picked',
            'delayed' => '⏰ Delayed',
            'failed' => '❌ Failed'
        ];

        return $statuses[$this->pickup_status] ?? ucfirst($this->pickup_status);
    }

    /**
     * Get delivery status display
     */
    public function getDeliveryStatusDisplayAttribute()
    {
        $statuses = [
            'pending' => '⏳ Pending',
            'in_transit' => '🚚 In Transit',
            'out_for_delivery' => '🚀 Out for Delivery',
            'delivered' => '✅ Delivered',
            'failed' => '❌ Failed',
            'returned' => '🔄 Returned'
        ];

        return $statuses[$this->delivery_status] ?? ucfirst($this->delivery_status);
    }

    /**
     * Check if pickup is delayed
     */
    public function getIsPickupDelayedAttribute()
    {
        if ($this->pickup_scheduled_at && !$this->pickup_actual_at) {
            return now() > $this->pickup_scheduled_at;
        }
        return false;
    }

    /**
     * Check if delivery is delayed
     */
    public function getIsDeliveryDelayedAttribute()
    {
        if ($this->delivery_estimated_at && !$this->delivery_actual_at) {
            return now() > $this->delivery_estimated_at;
        }
        return false;
    }

    /**
     * Get tracking URL from courier partner
     */
    public function getTrackingUrlAttribute()
    {
        if ($this->courierPartner && $this->courier_tracking_number) {
            return $this->courierPartner->getTrackingUrl($this->courier_tracking_number);
        }
        return null;
    }

    /* ==================== METHODS ==================== */

    /**
     * Update pickup status
     */
    public function updatePickupStatus($status, $actualTime = null)
    {
        $this->pickup_status = $status;

        if ($actualTime) {
            $this->pickup_actual_at = $actualTime;
        } elseif ($status === 'picked' && !$this->pickup_actual_at) {
            $this->pickup_actual_at = now();
        }

        if ($status === 'picked' && $this->status === 'pending') {
            $this->status = 'picked';
        }

        $this->save();

        return $this;
    }

    /**
     * Update delivery status
     */
    public function updateDeliveryStatus($status, $actualTime = null)
    {
        $this->delivery_status = $status;

        if ($actualTime) {
            $this->delivery_actual_at = $actualTime;
        } elseif ($status === 'delivered' && !$this->delivery_actual_at) {
            $this->delivery_actual_at = now();
        }

        if ($status === 'delivered') {
            $this->status = 'delivered';
        } elseif ($status === 'failed') {
            $this->status = 'failed';
        }

        $this->save();

        return $this;
    }

    /**
     * Save API response
     */
    public function saveApiResponse($response, $request = null)
    {
        if ($request) {
            $this->api_request = $request;
        }

        $this->api_response = $response;
        $this->save();

        return $this;
    }

    /**
     * Generate label
     */
    public function generateLabel()
    {
        // This would integrate with courier API
        // For now, return existing label or null
        return $this->label_url;
    }

    /**
     * Cancel with courier
     */
    public function cancelWithCourier()
    {
        // This would integrate with courier API
        $this->status = 'cancelled';
        $this->save();

        return $this;
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope by courier partner
     */
    public function scopeForCourier($query, $courierId)
    {
        return $query->where('courier_partner_id', $courierId);
    }

    /**
     * Scope by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope by pickup status
     */
    public function scopeWithPickupStatus($query, $status)
    {
        return $query->where('pickup_status', $status);
    }

    /**
     * Scope by delivery status
     */
    public function scopeWithDeliveryStatus($query, $status)
    {
        return $query->where('delivery_status', $status);
    }

    /**
     * Scope pending pickup
     */
    public function scopePendingPickup($query)
    {
        return $query->whereIn('pickup_status', ['scheduled', null])
                     ->whereNull('pickup_actual_at');
    }

    /**
     * Scope pending delivery
     */
    public function scopePendingDelivery($query)
    {
        return $query->whereIn('delivery_status', ['pending', 'in_transit', 'out_for_delivery'])
                     ->whereNull('delivery_actual_at');
    }

    /**
     * Scope delayed pickups
     */
    public function scopeDelayedPickup($query)
    {
        return $query->whereNotNull('pickup_scheduled_at')
                     ->whereNull('pickup_actual_at')
                     ->where('pickup_scheduled_at', '<', now());
    }

    /**
     * Scope delayed deliveries
     */
    public function scopeDelayedDelivery($query)
    {
        return $query->whereNotNull('delivery_estimated_at')
                     ->whereNull('delivery_actual_at')
                     ->where('delivery_estimated_at', '<', now());
    }

    /* ==================== STATIC METHODS ==================== */

    /**
     * Create from shipment
     */
    public static function createFromShipment(Shipment $shipment, $courierPartnerId)
    {
        return self::create([
            'shipment_id' => $shipment->id,
            'courier_partner_id' => $courierPartnerId,
            'status' => 'pending',
            'pickup_status' => 'scheduled'
        ]);
    }

    /**
     * Get dashboard statistics
     */
    public static function getDashboardStats()
    {
        return [
            'total' => self::count(),
            'pending_pickup' => self::pendingPickup()->count(),
            'pending_delivery' => self::pendingDelivery()->count(),
            'delivered' => self::where('status', 'delivered')->count(),
            'delayed_pickup' => self::delayedPickup()->count(),
            'delayed_delivery' => self::delayedDelivery()->count(),
            'total_charges' => self::sum('total_courier_charge')
        ];
    }
}
