<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomepageSlot extends Model
{
    protected $fillable = [
        'language_id',
        'key',
        'title',
        'slot_type',
        'sort_order',
        'is_active',
        'starts_at',
        'ends_at',
        'config_json',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'config_json' => 'array',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(HomepageSlotItem::class, 'slot_id')->orderBy('sort_order');
    }
}
