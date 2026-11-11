<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class QuizAttempt extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'quiz_id',
        'started_at',
        'expires_at',
        'submitted_at',
        'time_spent_seconds',
        'status',
        'score',
        'max_score',
        'marks_positive',
        'marks_negative',
        'correct_count',
        'incorrect_count',
        'unanswered_count',
        'marked_for_review_count',
        'accuracy_pct',
        'all_india_rank',
        'percentile',
        'tab_switches_count',
        'section_analytics_json',
        'ip_address',
        'user_agent',
    ];

    protected static function booted(): void
    {
        static::creating(function ($attempt) {
            if (empty($attempt->uuid)) {
                $attempt->uuid = (string) Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
            'submitted_at' => 'datetime',
            'score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'marks_positive' => 'decimal:2',
            'marks_negative' => 'decimal:2',
            'accuracy_pct' => 'decimal:2',
            'percentile' => 'decimal:2',
            'section_analytics_json' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    public function isExpired(): bool
    {
        if ($this->status === 'completed') {
            return false;
        }

        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    public function getRemainingSecondsAttribute(): int
    {
        if ($this->status === 'completed' || !$this->expires_at) {
            return 0;
        }

        return max(0, now()->diffInSeconds($this->expires_at, false));
    }
}
