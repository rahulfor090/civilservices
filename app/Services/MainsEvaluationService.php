<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\MainsEvaluationScore;
use App\Models\MainsSubmission;
use App\Models\MainsTest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MainsEvaluationService
{
    /**
     * Submit an answer copy PDF for evaluation.
     */
    public function submitCopy(User $user, MainsTest $test, string $submittedCopyPdf, ?string $candidateRollNo = null): MainsSubmission
    {
        return DB::transaction(function () use ($user, $test, $submittedCopyPdf, $candidateRollNo) {
            $submission = MainsSubmission::create([
                'user_id' => $user->id,
                'mains_test_id' => $test->id,
                'candidate_roll_no' => $candidateRollNo ?: ('UPSC-' . strtoupper(substr(uniqid(), -6))),
                'submitted_copy_pdf' => $submittedCopyPdf,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            // Notify student
            AppNotification::create([
                'user_id' => $user->id,
                'title' => 'Mains Answer Copy Submitted',
                'message' => "Your answer copy for '{$test->title}' has been submitted for evaluation.",
                'icon' => 'document-check',
                'action_url' => route('student.mains.index'),
            ]);

            return $submission;
        });
    }

    /**
     * Assign an evaluator to the submission.
     */
    public function assignEvaluator(MainsSubmission $submission, User $evaluator): void
    {
        $submission->update([
            'evaluator_id' => $evaluator->id,
            'status' => 'assigned',
        ]);

        AppNotification::create([
            'user_id' => $evaluator->id,
            'title' => 'New Mains Copy Assigned',
            'message' => "Answer copy #{$submission->id} ({$submission->test->title}) has been assigned to you for evaluation.",
            'icon' => 'pencil-square',
            'action_url' => route('admin.mains.evaluate', $submission),
        ]);
    }

    /**
     * Grade the copy with UPSC 4-dimension rubrics and complete evaluation.
     *
     * @param MainsSubmission $submission
     * @param array $rubrics Array of ['question_id' => int, 'intro_score' => float, 'content_score' => float, 'structure_score' => float, 'conclusion_score' => float, 'comments' => string]
     * @param string|null $evaluatedCopyPdf
     * @param string|null $generalRemarks
     * @return MainsSubmission
     */
    public function evaluateCopy(MainsSubmission $submission, array $rubrics, ?string $evaluatedCopyPdf = null, ?string $generalRemarks = null): MainsSubmission
    {
        return DB::transaction(function () use ($submission, $rubrics, $evaluatedCopyPdf, $generalRemarks) {
            $totalTestScore = 0.0;

            foreach ($rubrics as $rubric) {
                $qId = $rubric['question_id'];
                $intro = (float)($rubric['intro_score'] ?? 0);
                $content = (float)($rubric['content_score'] ?? 0);
                $structure = (float)($rubric['structure_score'] ?? 0);
                $conclusion = (float)($rubric['conclusion_score'] ?? 0);
                $questionScore = $intro + $content + $structure + $conclusion;
                $totalTestScore += $questionScore;

                MainsEvaluationScore::updateOrCreate(
                    [
                        'mains_submission_id' => $submission->id,
                        'mains_test_question_id' => $qId,
                    ],
                    [
                        'intro_score' => $intro,
                        'content_score' => $content,
                        'structure_score' => $structure,
                        'conclusion_score' => $conclusion,
                        'total_question_score' => $questionScore,
                        'evaluator_comments' => $rubric['comments'] ?? null,
                    ]
                );
            }

            $submission->update([
                'status' => 'evaluated',
                'total_score' => $totalTestScore,
                'evaluated_copy_pdf' => $evaluatedCopyPdf ?: $submission->evaluated_copy_pdf,
                'evaluator_remarks' => $generalRemarks,
                'evaluated_at' => now(),
            ]);

            // Notify candidate
            AppNotification::create([
                'user_id' => $submission->user_id,
                'title' => 'Evaluated Answer Copy Ready',
                'message' => "Your copy for '{$submission->test->title}' has been evaluated! Total Score: {$totalTestScore}/{$submission->test->total_marks}.",
                'icon' => 'academic-cap',
                'action_url' => route('student.mains.evaluated', $submission),
            ]);

            return $submission;
        });
    }
}
