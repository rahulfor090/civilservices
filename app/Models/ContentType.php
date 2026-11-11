<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'url_prefix',
        'template',
        'icon',
        'supports_translation',
        'is_hierarchical',
        'settings_json',
        'is_active',
    ];

    protected $casts = [
        'supports_translation' => 'boolean',
        'is_hierarchical' => 'boolean',
        'is_active' => 'boolean',
        'settings_json' => 'array',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
