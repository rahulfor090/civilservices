<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationMenu extends Model
{
    protected $fillable = ['code', 'name', 'language_id', 'is_active'];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'menu_id')->whereNull('parent_id')->orderBy('sort_order');
    }

    public function allItems(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'menu_id')->orderBy('sort_order');
    }
}
