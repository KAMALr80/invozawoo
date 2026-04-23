<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddress extends Model
{
    use SoftDeletes;

    protected $table = 'customer_addresses';

    protected $fillable = [
        'customer_id',
        'address_type',
        'name',
        'receiver_name',
        'receiver_phone',
        'alternate_phone',
        'address_line1',
        'address_line2',
        'landmark',
        'city',
        'state',
        'pincode',
        'country',
        'latitude',
        'longitude',
        'place_id',
        'is_default',
        'delivery_instructions',
        'is_active'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /* ==================== RELATIONSHIPS ==================== */

    /**
     * Get the customer for this address
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get shipments delivered to this address
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'shipping_address', 'address_line1')
                    ->where('city', $this->city)
                    ->where('pincode', $this->pincode);
    }

    /* ==================== ACCESSORS ==================== */

    /**
     * Get full address as string
     */
    public function getFullAddressAttribute()
    {
        $parts = [];

        if ($this->address_line1) $parts[] = $this->address_line1;
        if ($this->address_line2) $parts[] = $this->address_line2;
        if ($this->landmark) $parts[] = 'Near ' . $this->landmark;
        if ($this->city) $parts[] = $this->city;
        if ($this->state) $parts[] = $this->state;
        if ($this->pincode) $parts[] = $this->pincode;
        if ($this->country && $this->country !== 'India') $parts[] = $this->country;

        return implode(', ', $parts);
    }

    /**
     * Get short address (city, pincode)
     */
    public function getShortAddressAttribute()
    {
        $parts = [];
        if ($this->city) $parts[] = $this->city;
        if ($this->pincode) $parts[] = $this->pincode;

        return implode(' - ', $parts);
    }

    /**
     * Get one-line address
     */
    public function getOneLineAddressAttribute()
    {
        return $this->full_address;
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

        // Fallback to address search
        return "https://www.google.com/maps/search/?api=1&query=" . urlencode($this->full_address);
    }

    /**
     * Get address type with icon
     */
    public function getAddressTypeDisplayAttribute()
    {
        $types = [
            'home' => '🏠 Home',
            'office' => '🏢 Office',
            'other' => '📍 Other'
        ];

        return $types[$this->address_type] ?? ucfirst($this->address_type);
    }

    /**
     * Get formatted phone
     */
    public function getFormattedPhoneAttribute()
    {
        return $this->receiver_phone ?? $this->customer?->mobile;
    }

    /**
     * Get formatted alternate phone
     */
    public function getFormattedAlternatePhoneAttribute()
    {
        return $this->alternate_phone ?? 'Not provided';
    }

    /**
     * Get receiver full name
     */
    public function getReceiverFullNameAttribute()
    {
        return $this->receiver_name ?? $this->customer?->name;
    }

    /**
     * Get address summary for select
     */
    public function getSummaryAttribute()
    {
        return $this->short_address . ' - ' . $this->receiver_full_name . ' (' . $this->formatted_phone . ')';
    }

    /**
     * Check if address has coordinates
     */
    public function getHasCoordinatesAttribute()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

   /**
 * Check if this is default address
 */
public function getIsDefaultAttribute()
{
    return $this->attributes['is_default'] ?? false; // ✅ FIXED: Use attributes array
}

    /* ==================== METHODS ==================== */

    /**
     * Set as default address
     */
    public function setAsDefault()
    {
        // Remove default from all other addresses of this customer
        self::where('customer_id', $this->customer_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->is_default = true;
        $this->save();

        // Update customer's default address
        if ($this->customer) {
            $this->customer->default_address_id = $this->id;
            $this->customer->default_latitude = $this->latitude;
            $this->customer->default_longitude = $this->longitude;
            $this->customer->default_place_id = $this->place_id;
            $this->customer->save();
        }

        return $this;
    }

    /**
     * Update coordinates
     */
    public function updateCoordinates($latitude, $longitude, $placeId = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        if ($placeId) {
            $this->place_id = $placeId;
        }

        $this->save();

        // If this is default address, update customer's default coordinates
        if ($this->is_default && $this->customer) {
            $this->customer->default_latitude = $latitude;
            $this->customer->default_longitude = $longitude;
            $this->customer->default_place_id = $placeId;
            $this->customer->save();
        }

        return $this;
    }

    /**
     * Deactivate address
     */
    public function deactivate()
    {
        $this->is_active = false;
        $this->save();

        // If this was default, remove default
        if ($this->is_default) {
            $this->is_default = false;
            $this->save();

            // Set another address as default if available
            $otherAddress = self::where('customer_id', $this->customer_id)
                ->where('id', '!=', $this->id)
                ->where('is_active', true)
                ->first();

            if ($otherAddress) {
                $otherAddress->setAsDefault();
            }
        }

        return $this;
    }

    /**
     * Activate address
     */
    public function activate()
    {
        $this->is_active = true;
        $this->save();

        return $this;
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope by customer
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope active addresses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope default address
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope by address type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('address_type', $type);
    }

    /**
     * Scope by city
     */
    public function scopeInCity($query, $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    /**
     * Scope by pincode
     */
    public function scopeInPincode($query, $pincode)
    {
        return $query->where('pincode', $pincode);
    }

    /**
     * Scope addresses with coordinates
     */
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')
                     ->whereNotNull('longitude');
    }

    /**
     * Scope addresses without coordinates
     */
    public function scopeWithoutCoordinates($query)
    {
        return $query->whereNull('latitude')
                     ->orWhereNull('longitude');
    }

    /* ==================== STATIC METHODS ==================== */

    /**
     * Get or create default address for customer
     */
    public static function getOrCreateDefault($customerId)
    {
        $address = self::forCustomer($customerId)
            ->default()
            ->active()
            ->first();

        if (!$address) {
            $address = self::forCustomer($customerId)
                ->active()
                ->first();
        }

        return $address;
    }

    /**
     * Get customer's address book
     */
    public static function getAddressBook($customerId)
    {
        return [
            'default' => self::forCustomer($customerId)->default()->active()->first(),
            'home' => self::forCustomer($customerId)->ofType('home')->active()->get(),
            'office' => self::forCustomer($customerId)->ofType('office')->active()->get(),
            'other' => self::forCustomer($customerId)->ofType('other')->active()->get(),
            'all' => self::forCustomer($customerId)->active()->get()
        ];
    }

    /* ==================== BOOT METHOD ==================== */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($address) {
            if (empty($address->country)) {
                $address->country = 'India';
            }

            if (empty($address->address_type)) {
                $address->address_type = 'other';
            }

            if (empty($address->is_active)) {
                $address->is_active = true;
            }

            // If this is first address, make it default
            if (self::forCustomer($address->customer_id)->count() === 0) {
                $address->is_default = true;
            }
        });

        static::created(function ($address) {
            if ($address->is_default) {
                $address->setAsDefault();
            }
        });

        static::deleting(function ($address) {
            // If this was default, set another address as default
            if ($address->is_default) {
                $otherAddress = self::forCustomer($address->customer_id)
                    ->where('id', '!=', $address->id)
                    ->where('is_active', true)
                    ->first();

                if ($otherAddress) {
                    $otherAddress->setAsDefault();
                }
            }
        });
    }
}
