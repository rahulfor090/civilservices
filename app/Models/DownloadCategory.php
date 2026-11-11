<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DownloadCategory extends Model
{
    protected $fillable = [
        'parent_id',
        'language_id',
        'name',
        'slug',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DownloadCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(DownloadCategory::class, 'parent_id')->orderBy('sort_order');
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class, 'category_id')->orderBy('sort_order');
    }
}
