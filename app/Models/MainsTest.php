<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainsTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'language_id',
        'gs_paper',
        'total_marks',
        'duration_minutes',
        'question_paper_pdf',
        'model_answer_pdf',
        'instructions',
        'submission_deadline',
        'status',
    ];

    protected $casts = [
        'submission_deadline' => 'datetime',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(MainsTestQuestion::class)->orderBy('question_number');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(MainsSubmission::class);
    }
}
