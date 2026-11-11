<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MainsEvaluationScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'mains_submission_id',
        'mains_test_question_id',
        'intro_score',
        'content_score',
        'structure_score',
        'conclusion_score',
        'total_question_score',
        'evaluator_comments',
    ];

    protected $casts = [
        'intro_score' => 'float',
        'content_score' => 'float',
        'structure_score' => 'float',
        'conclusion_score' => 'float',
        'total_question_score' => 'float',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(MainsSubmission::class, 'mains_submission_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(MainsTestQuestion::class, 'mains_test_question_id');
    }
}
