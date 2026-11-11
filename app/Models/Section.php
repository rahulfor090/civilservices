<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = [
        'parent_id',
        'content_type_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Section::class, 'parent_id')->orderBy('sort_order');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SectionTranslation::class);
    }

    public function translation(?string $locale = null): ?SectionTranslation
    {
        $code = $locale ?? app()->getLocale();
        return $this->translations->first(function ($t) use ($code) {
            return $t->language && $t->language->code === $code;
        }) ?? $this->translations->first();
    }

    public function getNameAttribute(): string
    {
        return $this->translation()?->name ?? ('Section #' . $this->id);
    }

    public function getCodeAttribute(): string
    {
        $slug = $this->translation()?->slug;
        if ($slug) {
            if (preg_match('/general-studies-(\d+)/i', $slug, $m)) {
                return 'GS-' . $m[1];
            }
            return strtoupper($slug);
        }
        return 'GS-' . $this->id;
    }
}
