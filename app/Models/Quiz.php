<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'quiz_category_id',
        'language_id',
        'title',
        'slug',
        'instructions',
        'duration_minutes',
        'total_marks',
        'marks_per_question',
        'negative_marks_per_question',
        'pass_percentage',
        'is_free',
        'allow_reattempt',
        'shuffle_questions',
        'status',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_free' => 'boolean',
            'allow_reattempt' => 'boolean',
            'shuffle_questions' => 'boolean',
            'published_at' => 'datetime',
            'total_marks' => 'decimal:2',
            'marks_per_question' => 'decimal:2',
            'negative_marks_per_question' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuizCategory::class, 'quiz_category_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'quiz_questions')
            ->withPivot('section_name', 'sort_order')
            ->orderByPivot('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function getUrlAttribute(): string
    {
        $prefix = $this->language && $this->language->code === 'hi' ? '/hindi' : '';
        return url($prefix . '/quizzes/' . $this->slug);
    }
}
