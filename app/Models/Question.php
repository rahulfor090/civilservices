<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'language_id',
        'section_id',
        'stem',
        'explanation',
        'subject_tag',
        'topic_tag',
        'difficulty',
        'question_type',
        'default_marks',
        'default_negative_marks',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_marks' => 'decimal:2',
            'default_negative_marks' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('sort_order');
    }
}
