<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttemptAnswer extends Model
{
    protected $fillable = [
        'quiz_attempt_id',
        'question_id',
        'selected_option_ids',
        'is_marked_for_review',
        'is_answered',
        'is_correct',
        'marks_awarded',
        'time_spent_seconds',
    ];

    protected function casts(): array
    {
        return [
            'selected_option_ids' => 'array',
            'is_marked_for_review' => 'boolean',
            'is_answered' => 'boolean',
            'is_correct' => 'boolean',
            'marks_awarded' => 'decimal:2',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
