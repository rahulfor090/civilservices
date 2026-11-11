<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainsTestQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'mains_test_id',
        'question_number',
        'stem',
        'max_marks',
        'word_limit',
    ];

    protected $casts = [
        'max_marks' => 'float',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(MainsTest::class, 'mains_test_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(MainsEvaluationScore::class, 'mains_test_question_id');
    }
}
