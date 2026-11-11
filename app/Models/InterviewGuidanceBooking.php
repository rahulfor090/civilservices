<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewGuidanceBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'mobile',
        'email',
        'upsc_roll_number',
        'optional_subject',
        'daf_file_path',
        'medium',
        'interview_slot_date',
        'centre_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'interview_slot_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function centre(): BelongsTo
    {
        return $this->belongsTo(Centre::class);
    }
}
