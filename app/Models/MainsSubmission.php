<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainsSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mains_test_id',
        'candidate_roll_no',
        'submitted_copy_pdf',
        'evaluated_copy_pdf',
        'evaluator_id',
        'status',
        'total_score',
        'evaluator_remarks',
        'submitted_at',
        'evaluated_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'evaluated_at' => 'datetime',
        'total_score' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(MainsTest::class, 'mains_test_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(MainsEvaluationScore::class, 'mains_submission_id');
    }
}
