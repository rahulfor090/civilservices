<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Centre extends Model
{
    use HasFactory;

    public const CACHE_KEY_ACTIVE = 'centres_active_ordered';

    protected $fillable = [
        'name',
        'slug',
        'city',
        'state',
        'address',
        'pincode',
        'phone',
        'email',
        'google_maps_url',
        'latitude',
        'longitude',
        'facilities',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'facilities' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => self::clearCache());
        static::deleted(fn () => self::clearCache());
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ACTIVE);
    }

    /**
     * Retrieve all active centres ordered for display, cached across requests.
     */
    public static function activeOrdered(): Collection
    {
        try {
            return Cache::remember(self::CACHE_KEY_ACTIVE, 3600, function () {
                return self::where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();
            });
        } catch (\Throwable $e) {
            return new Collection();
        }
    }

    /**
     * Resolve the National Headquarters centre from active centres.
     */
    public static function headquarters(): ?self
    {
        $centres = self::activeOrdered();

        return $centres->firstWhere('slug', 'delhi-old-rajinder-nagar')
            ?? $centres->first(fn (self $centre) => $centre->isHeadquarters())
            ?? $centres->first();
    }

    public function isHeadquarters(): bool
    {
        return $this->slug === 'delhi-old-rajinder-nagar'
            || str_contains(strtolower($this->name), 'headquarters')
            || str_contains(strtolower($this->slug), 'rajinder');
    }

    public function fullAddress(): string
    {
        $address = trim((string) $this->address);
        $pincode = trim((string) $this->pincode);

        if ($pincode !== '' && !str_contains($address, $pincode)) {
            return $address . ', ' . $pincode;
        }

        return $address;
    }

    public function mapsUrl(): string
    {
        if (!empty($this->google_maps_url)) {
            return $this->google_maps_url;
        }

        return 'https://maps.google.com/?q=' . urlencode($this->fullAddress());
    }

    public function batches(): HasMany
    {
        return $this->hasMany(CourseBatch::class, 'centre_id');
    }

    public function counsellingBookings(): HasMany
    {
        return $this->hasMany(CounsellingBooking::class);
    }
}
