<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'folder_id',
        'disk',
        'path',
        'original_name',
        'stored_name',
        'extension',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'duration_seconds',
        'alt_text',
        'caption',
        'credit',
        'derivatives_json',
        'checksum',
        'uploaded_by',
        'usage_count',
    ];

    protected $casts = [
        'derivatives_json' => 'array',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(MediaUsage::class);
    }

    public function getUrlAttribute(): string
    {
        if (preg_match('#^https?://#i', $this->path)) {
            return $this->path;
        }
        return asset('storage/' . ltrim($this->path, '/'));
    }
}
