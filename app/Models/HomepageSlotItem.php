<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomepageSlotItem extends Model
{
    protected $fillable = [
        'slot_id',
        'item_type',
        'item_id',
        'image_media_id',
        'headline',
        'subheadline',
        'cta_label',
        'cta_url',
        'cta_label_alt',
        'cta_url_alt',
        'utm_json',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'utm_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function slot(): BelongsTo
    {
        return $this->belongsTo(HomepageSlot::class, 'slot_id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_media_id');
    }
}
