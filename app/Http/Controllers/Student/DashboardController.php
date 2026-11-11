<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Student LMS Dashboard Home
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Active Enrollments
        $enrollments = Enrollment::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['course.batches.lessons', 'batch'])
            ->latest('enrolled_at')
            ->get();

        // Recent Quiz Attempts & Scorecards
        $recentAttempts = QuizAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('quiz.category')
            ->latest('submitted_at')
            ->take(6)
            ->get();

        // Ongoing incomplete attempts
        $ongoingAttempts = QuizAttempt::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->with('quiz')
            ->latest('id')
            ->get();

        return view('student.dashboard', compact('user', 'enrollments', 'recentAttempts', 'ongoingAttempts'));
    }

    /**
     * Course player / playlist view
     */
    public function course(Request $request, string $slug): View
    {
        $user = auth()->user();

        $course = Course::where('slug', $slug)
            ->with([
                'batches.lessons.quiz.category',
                'batches.lessons.quiz.questions.options',
                'batches.lessons.mainsTest',
                'batches.lessons.progress' => fn($q) => $q->where('user_id', $user->id)
            ])
            ->firstOrFail();

        // Check enrollment or allow free previews
        $isEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        $batch = $course->batches->first();
        // Chronological sorting
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

        // Group by scheduled date for daily planner
        $groupedPlanner = $lessons->groupBy(function ($lesson) {
            return $lesson->scheduled_date ? $lesson->scheduled_date->format('Y-m-d') : 'Flexible Curriculum';
        });

        // Active lesson selection
        $activeLessonId = $request->query('lesson');
        $activeLesson = $lessons->firstWhere('id', $activeLessonId) ?? $lessons->first();

        $quizAttempt = null;
        if ($activeLesson && $activeLesson->quiz_id) {
            $quizAttempt = QuizAttempt::where('user_id', $user->id)
                ->where('quiz_id', $activeLesson->quiz_id)
                ->latest('id')
                ->first();
        }

        $mainsSubmission = null;
        if ($activeLesson && $activeLesson->mains_test_id) {
            $mainsSubmission = \App\Models\MainsSubmission::where('user_id', $user->id)
                ->where('mains_test_id', $activeLesson->mains_test_id)
                ->latest('id')
                ->first();
        }

        return view('student.courses.show', compact('course', 'batch', 'lessons', 'groupedPlanner', 'activeLesson', 'isEnrolled', 'quizAttempt', 'mainsSubmission'));
    }

    /**
     * Track video watch progress
     */
    public function trackProgress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lesson_id' => 'required|integer|exists:course_lessons,id',
            'watched_seconds' => 'required|integer|min:0',
            'is_completed' => 'boolean',
        ]);

        $progress = LessonProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'course_lesson_id' => $validated['lesson_id'],
            ],
            [
                'watched_seconds' => $validated['watched_seconds'],
                'is_completed' => $validated['is_completed'] ?? false,
                'last_watched_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }
}
