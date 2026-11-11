<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Lead extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'country_code',
        'state',
        'city',
        'language_id',
        'interest',
        'interest_tags_json',
        'source',
        'source_page',
        'utm_json',
        'message',
        'status',
        'assigned_to',
        'centre_id',
        'consent',
        'follow_up_at',
    ];

    protected $casts = [
        'interest_tags_json' => 'array',
        'utm_json' => 'array',
        'consent' => 'boolean',
        'follow_up_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($lead) {
            if (empty($lead->uuid)) {
                $lead->uuid = (string) Str::uuid();
            }
        });
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function assignedCounsellor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function centre(): BelongsTo
    {
        return $this->belongsTo(Centre::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->orderByDesc('created_at');
    }
}
