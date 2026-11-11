<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Language;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Public Course Catalog / Listing
     */
    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $lang = Language::where('code', $locale)->first() ?? Language::where('code', 'en')->first();

        $query = Course::published()
            ->with(['batches.lessons', 'language'])
            ->withCount('enrollments');

        if ($request->filled('exam')) {
            $query->where('target_exam', $request->exam);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $courses = $query->latest('id')->paginate(12)->withQueryString();

        return view('public.courses.index', compact('courses', 'locale'));
    }

    /**
     * Public Course Details Landing Page
     */
    public function show(Request $request, string $slug): View
    {
        $locale = app()->getLocale();
        $course = Course::where('slug', $slug)
            ->published()
            ->with(['batches.lessons.quiz', 'batches.lessons.mainsTest', 'language'])
            ->firstOrFail();

        $user = Auth::user();
        $isEnrolled = false;

        if ($user) {
            $isEnrolled = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->exists();
        }

        $batch = $course->batches->first();
        // Chronological sort by scheduled_at, scheduled_date, sort_order
        $lessons = $batch 
            ? $batch->lessons->sort(function ($a, $b) {
                if ($a->scheduled_at && $b->scheduled_at) {
                    return $a->scheduled_at <=> $b->scheduled_at;
                }
                if ($a->scheduled_date && $b->scheduled_date) {
                    return $a->scheduled_date <=> $b->scheduled_date;
                }
                return $a->sort_order <=> $b->sort_order;
            }) 
            : collect();

        // Group planner by scheduled date
        $groupedPlanner = $lessons->groupBy(function ($lesson) {
            return $lesson->scheduled_date ? $lesson->scheduled_date->format('Y-m-d') : 'Flexible Curriculum';
        });

        return view('public.courses.show', compact('course', 'batch', 'lessons', 'groupedPlanner', 'isEnrolled', 'locale'));
    }

    /**
     * Process Course Enrollment / Purchase
     */
    public function enroll(Request $request, Course $course): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('register', [
                'redirect' => route('courses.public.show', $course->slug)
            ])->with('info', "Please register or sign in to enroll in '{$course->title}'.");
        }

        $user = Auth::user();

        // Check if already active
        $existing = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return redirect()->route('student.courses.show', $course->slug)
                ->with('info', "You are already actively enrolled in this course. Opening lecture player.");
        }

        $batch = $course->batches()->where('is_enrolment_open', true)->first() ?? $course->batches()->first();

        $enrollment = Enrollment::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ],
            [
                'course_batch_id' => $batch?->id,
                'status' => 'active',
                'enrolled_at' => now(),
                'expires_at' => now()->addDays($course->validity_days ?: 365),
            ]
        );

        // Assign paid_student role
        $paidRole = Role::where('slug', 'paid_student')->first();
        if ($paidRole && !$user->roles->contains('id', $paidRole->id)) {
            $user->roles()->syncWithoutDetaching([$paidRole->id]);
        }

        AuditLog::record($user, 'course', 'enroll', $course, null, [
            'enrollment_id' => $enrollment->id,
            'batch_id' => $batch?->id,
            'price' => $course->price,
        ]);

        return redirect()->route('student.courses.show', $course->slug)
            ->with('success', "🎉 Enrollment Successful! Welcome to {$course->title}. Your live and recorded classes are now available.");
    }

    /**
     * Generate and download .ics iCalendar planner file with 30-minute reminder alarms
     */
    public function downloadCalendar(Course $course)
    {
        $batch = $course->batches()->first();
        $lessons = $batch ? $batch->lessons : collect();

        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Civil Service Gurukul//Course Planner//EN\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:PUBLISH\r\n";
        $ics .= "X-WR-CALNAME:Civil Service Gurukul - " . $course->title . "\r\n";
        $ics .= "X-WR-TIMEZONE:Asia/Kolkata\r\n";

        foreach ($lessons as $lesson) {
            if (!$lesson->scheduled_at) {
                continue;
            }

            $dtStart = $lesson->scheduled_at->format('Ymd\THis');
            $dtEnd = $lesson->scheduled_at->copy()->addMinutes($lesson->duration_minutes ?: 60)->format('Ymd\THis');
            $uid = "lesson-" . $lesson->id . "-" . md5($lesson->title) . "@civilservicegurukul.com";
            $summary = "CS Gurukul: [" . $lesson->type_badge_label . "] " . $lesson->title;
            $description = "Course: " . $course->title . "\\nType: " . $lesson->type_badge_label;
            
            if ($lesson->mentor_name) {
                $description .= "\\nMentor: " . $lesson->mentor_name;
            }
            if ($lesson->mentor_phone) {
                $description .= "\\nHelpline Phone: " . $lesson->mentor_phone;
            }
            if ($lesson->call_window) {
                $description .= "\\nCall Window: " . $lesson->call_window;
            }
            if ($lesson->meeting_link) {
                $description .= "\\nMeeting Link: " . $lesson->meeting_link;
            }

            $ics .= "BEGIN:VEVENT\r\n";
            $ics .= "UID:{$uid}\r\n";
            $ics .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
            $ics .= "DTSTART;TZID=Asia/Kolkata:{$dtStart}\r\n";
            $ics .= "DTEND;TZID=Asia/Kolkata:{$dtEnd}\r\n";
            $ics .= "SUMMARY:{$summary}\r\n";
            $ics .= "DESCRIPTION:{$description}\r\n";
            $ics .= "STATUS:CONFIRMED\r\n";
            // 30 minute advance alarm notification!
            $ics .= "BEGIN:VALARM\r\n";
            $ics .= "TRIGGER:-PT30M\r\n";
            $ics .= "ACTION:DISPLAY\r\n";
            $ics .= "DESCRIPTION:Reminder: {$summary} starts in 30 minutes at " . $lesson->scheduled_at->format('h:i A') . "\r\n";
            $ics .= "END:VALARM\r\n";
            $ics .= "END:VEVENT\r\n";
        }

        $ics .= "END:VCALENDAR\r\n";

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . \Illuminate\Support\Str::slug($course->title) . '-planner.ics"',
        ]);
    }

    /**
     * Subscribe student to schedule timeline alerts
     */
    public function subscribeAlerts(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        $targetEmail = $validated['email'] ?? ($user ? $user->email : null);
        $targetPhone = $validated['phone'] ?? ($user ? $user->phone : null);

        if ($targetEmail || $targetPhone) {
            \App\Models\Lead::create([
                'name' => $user ? $user->name : 'Course Aspirant',
                'email' => $targetEmail,
                'phone' => $targetPhone,
                'source' => 'course_planner_alert',
                'interest_level' => 'high',
                'notes' => "Subscribed to timeline & 30-min class alerts for: " . $course->title,
                'status' => 'new',
            ]);
        }

        return back()->with('success', "🔔 Schedule Alerts Activated! You will receive reminder notifications 30 minutes before each scheduled session.");
    }
}
