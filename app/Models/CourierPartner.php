<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourierPartner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // Basic Information
        'name',
        'code',

        // ✅ NEW: Contact Information
        'contact_person',
        'contact_email',
        'contact_phone',
        'address',

        // API Configuration
        'api_url',
        'api_key',
        'api_secret',
        'api_config',

        // ✅ NEW: Service Details
        'serviceable_cities',
        'serviceable_pincodes',
        'delivery_days',
        'cutoff_time',
        'holidays',

        // Pricing
        'rate_card',
        'weight_slabs',
        'cod_charges',

        // ✅ NEW: Volume Based Pricing
        'volumetric_factor',

        // Services
        'supported_services',

        // ✅ NEW: Tracking
        'tracking_url',

        // ✅ NEW: Label Settings
        'label_format',
        'label_size',

        // ✅ NEW: Integration
        'integration_type',

        // ✅ NEW: Branding
        'logo',
        'description',

        // Status
        'is_active',
        'priority'
    ];

    protected $casts = [
        // JSON fields
        'api_config' => 'array',
        'rate_card' => 'array',
        'weight_slabs' => 'array',
        'cod_charges' => 'array',
        'serviceable_pincodes' => 'array',
        'serviceable_cities' => 'array',
        'supported_services' => 'array',
        'delivery_days' => 'array',
        'holidays' => 'array',

        // Booleans
        'is_active' => 'boolean',

        // Numbers
        'priority' => 'integer',
        'volumetric_factor' => 'decimal:2',

        // Time
        'cutoff_time' => 'datetime:H:i',

        // Dates
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /* ==================== EXISTING RELATIONSHIPS ==================== */

    /**
     * Get shipments for this courier partner
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'courier_partner', 'name');
    }

    /* ==================== NEW RELATIONSHIPS ==================== */

    /**
     * Get courier shipments for this partner
     */
    public function courierShipments()
    {
        return $this->hasMany(CourierShipment::class);
    }

    /**
     * Get active courier shipments
     */
    public function activeShipments()
    {
        return $this->courierShipments()
                    ->whereIn('status', ['pending', 'picked', 'in_transit']);
    }

    /**
     * Get completed courier shipments
     */
    public function completedShipments()
    {
        return $this->courierShipments()
                    ->where('status', 'delivered');
    }

    /* ==================== EXISTING SCOPES ==================== */

    /**
     * Scope for active couriers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /* ==================== NEW SCOPES ==================== */

    /**
     * Scope couriers by priority order
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    /**
     * Scope couriers supporting specific service
     */
    public function scopeSupportsService($query, $service)
    {
        return $query->whereJsonContains('supported_services', $service);
    }

    /**
     * Scope couriers serving a pincode
     */
    public function scopeServesPincode($query, $pincode)
    {
        return $query->where(function($q) use ($pincode) {
            $q->whereNull('serviceable_pincodes')
              ->orWhereJsonContains('serviceable_pincodes', $pincode);
        });
    }

    /**
     * Scope couriers serving a city
     */
    public function scopeServesCity($query, $city)
    {
        return $query->where(function($q) use ($city) {
            $q->whereNull('serviceable_cities')
              ->orWhereJsonContains('serviceable_cities', $city);
        });
    }

    /**
     * Scope couriers with COD support
     */
    public function scopeSupportsCod($query)
    {
        return $query->whereNotNull('cod_charges');
    }

    /**
     * Scope couriers by integration type
     */
    public function scopeWithIntegration($query, $type)
    {
        return $query->where('integration_type', $type);
    }

    /* ==================== ACCESSORS ==================== */

    /**
     * Get display name with code
     */
    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * Get formatted priority
     */
    public function getPriorityDisplayAttribute()
    {
        $priorities = [
            1 => 'Highest',
            2 => 'High',
            3 => 'Normal',
            4 => 'Low',
            5 => 'Lowest'
        ];

        return $priorities[$this->priority] ?? 'Normal';
    }

    /**
     * Get serviceable pincodes as comma separated string
     */
    public function getPincodesListAttribute()
    {
        if (!$this->serviceable_pincodes) {
            return 'All pincodes';
        }

        $count = count($this->serviceable_pincodes);
        $sample = array_slice($this->serviceable_pincodes, 0, 5);

        if ($count <= 5) {
            return implode(', ', $sample);
        }

        return implode(', ', $sample) . " and " . ($count - 5) . " more";
    }

    /**
     * Get supported services as comma separated string
     */
    public function getServicesListAttribute()
    {
        if (!$this->supported_services) {
            return 'Standard only';
        }

        $services = array_map(function($service) {
            return ucfirst($service);
        }, $this->supported_services);

        return implode(', ', $services);
    }

    /**
     * Get formatted cutoff time
     */
    public function getFormattedCutoffAttribute()
    {
        return $this->cutoff_time ? $this->cutoff_time->format('h:i A') : 'Not set';
    }

    /**
     * Get delivery days as string
     */
    public function getDeliveryDaysDisplayAttribute()
    {
        if (!$this->delivery_days) {
            return 'Mon-Sat';
        }

        $days = [
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat',
            7 => 'Sun'
        ];

        $selected = [];
        foreach ($this->delivery_days as $day) {
            $selected[] = $days[$day] ?? $day;
        }

        return implode(', ', $selected);
    }

    /**
     * Get today is delivery day
     */
    public function getIsDeliveryDayTodayAttribute()
    {
        if (!$this->delivery_days) {
            return true;
        }

        $today = now()->dayOfWeekIso; // 1-7 (Mon-Sun)
        return in_array($today, $this->delivery_days);
    }

    /**
     * Get tracking URL with placeholder
     */
    public function getTrackingUrlFormatAttribute()
    {
        return $this->tracking_url ?? '#';
    }

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    /**
     * Check if cutoff time has passed today
     */
    public function getHasCutoffPassedAttribute()
    {
        if (!$this->cutoff_time) {
            return false;
        }

        return now() > $this->cutoff_time;
    }

    /* ==================== EXISTING METHODS ==================== */

    /**
     * Get rate for weight
     */
    public function getRateForWeight($weight)
    {
        if (!$this->rate_card) {
            return null;
        }

        $rates = $this->rate_card;
        ksort($rates);

        $selectedRate = null;
        foreach ($rates as $maxWeight => $rate) {
            if ($weight <= $maxWeight) {
                $selectedRate = $rate;
                break;
            }
        }

        return $selectedRate ?? end($rates);
    }

    /**
     * Check if pincode is serviceable
     */
    public function isPincodeServiceable($pincode)
    {
        if (!$this->serviceable_pincodes) {
            return true;
        }

        return in_array($pincode, $this->serviceable_pincodes);
    }

    /* ==================== NEW METHODS ==================== */

    /**
     * Check if city is serviceable
     */
    public function isCityServiceable($city)
    {
        if (!$this->serviceable_cities) {
            return true;
        }

        return in_array($city, $this->serviceable_cities);
    }

    /**
     * Calculate shipping charge based on weight and dimensions
     */
    public function calculateShippingCharge($weight, $length = null, $width = null, $height = null)
    {
        // Calculate volumetric weight if dimensions provided
        if ($length && $width && $height) {
            $volumetricFactor = $this->volumetric_factor ?? 5000;
            $volumetricWeight = ($length * $width * $height) / $volumetricFactor;
            $chargeableWeight = max($weight, $volumetricWeight);
        } else {
            $chargeableWeight = $weight;
        }

        // Get rate from weight slabs
        if ($this->weight_slabs) {
            return $this->getRateFromSlabs($chargeableWeight);
        }

        // Fallback to rate card
        return $this->getRateForWeight($chargeableWeight);
    }

    /**
     * Get rate from weight slabs
     */
    private function getRateFromSlabs($weight)
    {
        $slabs = $this->weight_slabs;

        if (!$slabs) {
            return null;
        }

        usort($slabs, function($a, $b) {
            return $a['max_weight'] <=> $b['max_weight'];
        });

        foreach ($slabs as $slab) {
            if ($weight <= $slab['max_weight']) {
                return $slab['rate'];
            }
        }

        return end($slabs)['rate'] ?? null;
    }

    /**
     * Calculate COD charges
     */
    public function calculateCodCharge($amount)
    {
        if (!$this->cod_charges) {
            return 0;
        }

        $charges = $this->cod_charges;

        if (isset($charges['percentage'])) {
            return ($amount * $charges['percentage']) / 100;
        }

        if (isset($charges['slabs'])) {
            foreach ($charges['slabs'] as $slab) {
                if ($amount <= $slab['max_amount']) {
                    return $slab['charge'];
                }
            }
        }

        return $charges['default'] ?? 0;
    }

    /**
     * Check if today is a holiday
     */
    public function isHolidayToday()
    {
        if (!$this->holidays) {
            return false;
        }

        $today = now()->format('Y-m-d');
        return in_array($today, $this->holidays);
    }

    /**
     * Get next pickup date
     */
    public function getNextPickupDate()
    {
        $date = now();

        // Skip holidays
        while ($this->isHoliday($date)) {
            $date->addDay();
        }

        // Check if delivery day
        while (!$this->isDeliveryDay($date)) {
            $date->addDay();
        }

        return $date;
    }

    /**
     * Check if date is a delivery day
     */
    public function isDeliveryDay($date)
    {
        if (!$this->delivery_days) {
            return true;
        }

        return in_array($date->dayOfWeekIso, $this->delivery_days);
    }

    /**
     * Check if date is a holiday
     */
    public function isHoliday($date)
    {
        if (!$this->holidays) {
            return false;
        }

        return in_array($date->format('Y-m-d'), $this->holidays);
    }

    /**
     * Get tracking URL for a specific tracking number
     */
    public function getTrackingUrl($trackingNumber)
    {
        if (!$this->tracking_url) {
            return null;
        }

        return str_replace('{tracking_number}', $trackingNumber, $this->tracking_url);
    }

    /**
     * Get courier dashboard statistics
     */
    public function getDashboardStats()
    {
        return [
            'total_shipments' => $this->shipments()->count(),
            'active_shipments' => $this->activeShipments()->count(),
            'completed_shipments' => $this->completedShipments()->count(),
            'total_revenue' => $this->shipments()->sum('total_charge'),
            'cod_collected' => $this->shipments()->where('payment_mode', 'cod')->sum('cod_charge'),
            'avg_delivery_time' => $this->calculateAvgDeliveryTime()
        ];
    }

    /**
     * Calculate average delivery time
     */
    private function calculateAvgDeliveryTime()
    {
        return $this->completedShipments()
            ->whereNotNull('delivery_actual_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, delivery_actual_at)) as avg_time')
            ->value('avg_time');
    }

    /**
     * Get best courier for shipment
     */
    public static function getBestCourier($pincode, $weight, $isCod = false, $service = 'standard')
    {
        $query = self::active()
            ->byPriority()
            ->servesPincode($pincode)
            ->supportsService($service);

        if ($isCod) {
            $query->supportsCod();
        }

        $couriers = $query->get();

        foreach ($couriers as $courier) {
            // Check cutoff time
            if ($courier->has_cutoff_passed && !$courier->is_delivery_day_today) {
                continue;
            }

            // Check holiday
            if ($courier->isHolidayToday()) {
                continue;
            }

            return $courier;
        }

        return null;
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($courier) {
            if (empty($courier->priority)) {
                $courier->priority = 99;
            }

            if (empty($courier->integration_type)) {
                $courier->integration_type = 'manual';
            }

            if (empty($courier->label_format)) {
                $courier->label_format = 'pdf';
            }

            if (empty($courier->label_size)) {
                $courier->label_size = 'a4';
            }
        });

        static::saving(function ($courier) {
            // Ensure priority is positive
            if ($courier->priority < 1) {
                $courier->priority = 1;
            }
        });
    }
}
