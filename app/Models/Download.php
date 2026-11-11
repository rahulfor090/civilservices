<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    protected $fillable = [
        'category_id',
        'language_id',
        'title',
        'description',
        'media_id',
        'file_path',
        'file_size',
        'exam',
        'year',
        'is_gated',
        'requires_login',
        'download_count',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_gated' => 'boolean',
        'requires_login' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DownloadCategory::class, 'category_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
