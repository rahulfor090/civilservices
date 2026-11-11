<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lead;
use App\Models\MainsSubmission;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        $metrics = [
            'total_leads' => Lead::count(),
            'converted_leads' => Lead::where('status', 'won')->count(),
            'quiz_attempts' => QuizAttempt::count(),
            'mains_submissions' => MainsSubmission::count(),
            'active_students' => Enrollment::where('status', 'active')->distinct('user_id')->count(),
            'courses_offered' => Course::count(),
        ];

        return view('admin.reports.index', compact('metrics'));
    }

    public function exportLeads(): StreamedResponse
    {
        $fileName = 'leads_export_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Mobile', 'Source', 'Status', 'Centre', 'Counsellor', 'Follow Up At', 'Created At']);

            Lead::with(['centre', 'assignedCounsellor'])->chunk(200, function ($leads) use ($handle) {
                foreach ($leads as $lead) {
                    fputcsv($handle, [
                        $lead->id,
                        $lead->name,
                        $lead->email,
                        $lead->mobile,
                        $lead->source,
                        $lead->status,
                        $lead->centre?->name ?? 'Online',
                        $lead->assignedCounsellor?->name ?? 'Unassigned',
                        $lead->follow_up_at?->toDateTimeString() ?? 'None',
                        $lead->created_at->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportQuizPerformance(): StreamedResponse
    {
        $fileName = 'quiz_performance_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Attempt ID', 'Candidate', 'Quiz Title', 'Score', 'Correct', 'Incorrect', 'Accuracy %', 'Status', 'Started At', 'Submitted At']);

            QuizAttempt::with(['user', 'quiz'])->chunk(200, function ($attempts) use ($handle) {
                foreach ($attempts as $a) {
                    fputcsv($handle, [
                        $a->id,
                        $a->user?->name ?? 'N/A',
                        $a->quiz?->title ?? 'N/A',
                        $a->score,
                        $a->correct_count,
                        $a->incorrect_count,
                        $a->accuracy_pct . '%',
                        $a->status,
                        $a->started_at?->toDateTimeString(),
                        $a->submitted_at?->toDateTimeString() ?? 'Incomplete',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
