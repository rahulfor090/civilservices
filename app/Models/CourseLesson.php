<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class CourseLesson extends Model
{
    protected $fillable = [
        'course_batch_id',
        'quiz_id',
        'mains_test_id',
        'title',
        'lesson_type',
        'activity_type',
        'scheduled_date',
        'scheduled_time',
        'scheduled_at',
        'unlock_at',
        'video_url',
        'notes_pdf_url',
        'duration_minutes',
        'sort_order',
        'is_free_preview',
        'mentor_name',
        'mentor_phone',
        'call_window',
        'session_instructions',
        'meeting_platform',
        'meeting_link',
        'meeting_password',
        'mains_question_text',
        'mains_marks',
        'mains_word_limit',
        'description',
        'alert_minutes_before',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'scheduled_at' => 'datetime',
            'unlock_at' => 'datetime',
            'is_free_preview' => 'boolean',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(CourseBatch::class, 'course_batch_id');
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function mainsTest(): BelongsTo
    {
        return $this->belongsTo(MainsTest::class, 'mains_test_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Effective activity type: video, live, quiz, mains, phone_session, test_series, pdf
     */
    public function getEffectiveTypeAttribute(): string
    {
        return $this->activity_type ?: ($this->lesson_type ?: 'video');
    }

    /**
     * Automatically convert YouTube/Vimeo links to embeddable player URLs
     */
    public function getEmbedVideoUrlAttribute(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }

        $url = trim($this->video_url);

        // 1. YouTube conversions (watch?v=, youtu.be/, embed/, live/, shorts/)
        if (preg_match('#(?:youtube\.com/(?:watch\?v=|embed/|live/|shorts/)|youtu\.be/)([a-zA-Z0-9_\-]{6,15})#i', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
        }

        // Just youtube.com homepage without a video ID cannot be loaded in an iframe
        if (preg_match('#^https?://(www\.)?youtube\.com/?$#i', $url)) {
            return null;
        }

        // 2. Vimeo conversions
        if (preg_match('#vimeo\.com/([0-9]+)#i', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return $url;
    }

    /**
     * Check if lesson is currently unlocked based on scheduled time
     */
    public function getIsUnlockedAttribute(): bool
    {
        if ($this->is_free_preview) {
            return true;
        }

        $unlockTime = $this->unlock_at ?: $this->scheduled_at;
        if (!$unlockTime) {
            return true;
        }

        // Civil Service Gurukul schedules in Indian Standard Time (Asia/Kolkata)
        $raw = $this->getRawOriginal('unlock_at') 
            ?: $this->getRawOriginal('scheduled_at') 
            ?: $unlockTime->format('Y-m-d H:i:s');
        $target = \Illuminate\Support\Carbon::parse($raw, 'Asia/Kolkata');

        return now('Asia/Kolkata')->greaterThanOrEqualTo($target);
    }

    /**
     * Alert timestamp (e.g. 30 mins before scheduled time: 4:30 PM for 5:00 PM session)
     */
    public function getAlertTimeAttribute(): ?Carbon
    {
        if (!$this->scheduled_at) {
            return null;
        }
        $minutes = $this->alert_minutes_before ?: 30;
        return $this->scheduled_at->copy()->subMinutes($minutes);
    }

    /**
     * Formatted label for badges
     */
    public function getTypeBadgeLabelAttribute(): string
    {
        return match ($this->effective_type) {
            'video' => 'Pre-Recorded Class',
            'live' => 'Live Interactive Video',
            'quiz' => 'Prelims Test / Quiz',
            'mains' => 'Mains Answer Writing',
            'phone_session' => 'Phone Interactive Session',
            'test_series' => 'Full Test Series',
            'pdf' => 'Study Notes & Compendium',
            default => 'Lecture'
        };
    }

    /**
     * Badge visual styling
     */
    public function getTypeBadgeColorAttribute(): string
    {
        return match ($this->effective_type) {
            'video' => 'bg-amber-100 text-amber-900 border-amber-300',
            'live' => 'bg-rose-100 text-rose-900 border-rose-300',
            'quiz' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
            'mains' => 'bg-indigo-100 text-indigo-900 border-indigo-300',
            'phone_session' => 'bg-sky-100 text-sky-900 border-sky-300',
            'test_series' => 'bg-purple-100 text-purple-900 border-purple-300',
            'pdf' => 'bg-slate-100 text-slate-900 border-slate-300',
            default => 'bg-amber-100 text-amber-900 border-amber-300'
        };
    }
}
