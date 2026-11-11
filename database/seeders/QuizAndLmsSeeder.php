<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseLesson;
use App\Models\Enrollment;
use App\Models\Language;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizCategory;
use App\Models\User;
use App\Services\QuizScoringService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuizAndLmsSeeder extends Seeder
{
    public function run(): void
    {
        $langEn = Language::where('code', 'en')->firstOrFail();
        $langHi = Language::where('code', 'hi')->firstOrFail();
        $adminUser = User::where('email', 'admin@civilservicegurukul.com')->first();
        $studentUser = User::where('email', 'student@civilservicegurukul.com')->first();

        // 1. Quiz Categories
        $catDnaEn = QuizCategory::firstOrCreate(
            ['slug' => 'daily-current-affairs-quiz', 'language_id' => $langEn->id],
            [
                'name' => 'Daily Current Affairs Quiz',
                'description' => 'Test your daily newspaper reading and editorial retention.',
                'icon' => 'calendar',
                'sort_order' => 1,
            ]
        );

        $catDnaHi = QuizCategory::firstOrCreate(
            ['slug' => 'daily-current-affairs-quiz', 'language_id' => $langHi->id],
            [
                'name' => 'दैनिक समसामयिकी क्विज़',
                'description' => 'दैनिक समाचार पत्र और संपादकीय अध्ययन पर आधारित वस्तुनिष्ठ प्रश्न।',
                'icon' => 'calendar',
                'sort_order' => 1,
            ]
        );

        $catGsMockEn = QuizCategory::firstOrCreate(
            ['slug' => 'upsc-prelims-gs-mock', 'language_id' => $langEn->id],
            [
                'name' => 'UPSC Prelims GS Mock Tests',
                'description' => 'Full-length and sectional mocks matching UPSC standard negative marking.',
                'icon' => 'check-circle',
                'sort_order' => 2,
            ]
        );

        // 2. Questions Bank (Sample Quality UPSC Prelims Questions)
        $questionsData = [
            [
                'stem' => 'With reference to the Indian Constitution, consider the following statements regarding the <strong>Pardoning Power of the Governor</strong>:<br>1. The Governor can pardon, reprieve, or commute a sentence of death.<br>2. The Governor’s pardoning power under Article 161 is subject to judicial review on grounds of arbitrariness.<br>Which of the statements given above is/are correct?',
                'explanation' => 'Statement 1 is incorrect because only the President can pardon a death sentence under Article 72. Statement 2 is correct because the Supreme Court in the Epuru Sudhakar case held that the pardoning power of both the President and Governor is subject to judicial review.',
                'subject' => 'Polity & Governance',
                'options' => [
                    ['text' => '1 only', 'is_correct' => false],
                    ['text' => '2 only', 'is_correct' => true],
                    ['text' => 'Both 1 and 2', 'is_correct' => false],
                    ['text' => 'Neither 1 nor 2', 'is_correct' => false],
                ],
            ],
            [
                'stem' => 'Consider the following statements regarding the <strong>Western Ghats Ecology Expert Panel (WGEEP)</strong> headed by Madhav Gadgil:<br>1. It recommended designating the entire Western Ghats region as an Ecologically Sensitive Area (ESA).<br>2. It recommended a complete blanket ban on all development activities across all zones.<br>Which of the statements given above is/are correct?',
                'explanation' => 'Statement 1 is correct. Statement 2 is incorrect because the Gadgil committee proposed a graded approach into three Ecologically Sensitive Zones (ESZ-1, ESZ-2, ESZ-3) with varied regulatory strictness, not an outright blanket ban across all zones.',
                'subject' => 'Environment & Ecology',
                'options' => [
                    ['text' => '1 only', 'is_correct' => true],
                    ['text' => '2 only', 'is_correct' => false],
                    ['text' => 'Both 1 and 2', 'is_correct' => false],
                    ['text' => 'Neither 1 nor 2', 'is_correct' => false],
                ],
            ],
            [
                'stem' => 'Which of the following is the primary objective of the <strong>PM Surya Ghar: Muft Bijli Yojana</strong> recently launched by the Government of India?',
                'explanation' => 'PM Surya Ghar: Muft Bijli Yojana aims to provide free electricity up to 300 units every month to 1 crore households by installing rooftop solar systems.',
                'subject' => 'Economy & Energy',
                'options' => [
                    ['text' => 'Providing solar-powered agricultural pumps to all marginal farmers', 'is_correct' => false],
                    ['text' => 'Installing rooftop solar systems to provide up to 300 units of free electricity monthly to one crore households', 'is_correct' => true],
                    ['text' => 'Replacing all thermal power plants in northern India with nuclear reactors by 2030', 'is_correct' => false],
                    ['text' => 'Supplying free lithium-ion batteries for electric two-wheelers in tier-2 cities', 'is_correct' => false],
                ],
            ],
            [
                'stem' => 'In the context of international relations, the term <strong>"Golden Crescent"</strong> is frequently mentioned in the news in relation to:',
                'explanation' => 'The Golden Crescent refers to the illicit opium production region in South-Western Asia, encompassing Afghanistan, Iran, and Pakistan.',
                'subject' => 'Internal Security',
                'options' => [
                    ['text' => 'The semiconductor manufacturing belt in East Asia', 'is_correct' => false],
                    ['text' => 'The historic maritime spice trade route spanning the Arabian Sea', 'is_correct' => false],
                    ['text' => 'The illicit opium and narcotics trafficking hub covering Afghanistan, Iran, and Pakistan', 'is_correct' => true],
                    ['text' => 'The regional solar energy cooperative alliance between Middle Eastern nations', 'is_correct' => false],
                ],
            ],
            [
                'stem' => 'With reference to Ancient Indian history, what was the primary significance of the <strong>Kanaganahalli excavation site</strong> located in Karnataka?',
                'explanation' => 'Kanaganahalli revealed an Adholoka Maha Stupa featuring a unique sculpted stone relief panel of Emperor Ashoka inscribed with "Ranyo Asoko".',
                'subject' => 'Art & Culture',
                'options' => [
                    ['text' => 'It revealed the largest Harappan dockyard outside Gujarat', 'is_correct' => false],
                    ['text' => 'It yielded an inscribed portrait stone panel of Emperor Ashoka bearing the label "Ranyo Asoko"', 'is_correct' => true],
                    ['text' => 'It was the principal mint city of the Gupta empire', 'is_correct' => false],
                    ['text' => 'It contained the oldest Iron Age megalithic burial mounds in the Deccan', 'is_correct' => false],
                ],
            ],
        ];

        // 3. Create English Quiz
        $quizEn = Quiz::firstOrCreate(
            ['slug' => 'daily-current-affairs-quiz-september-18', 'language_id' => $langEn->id],
            [
                'quiz_category_id' => $catDnaEn->id,
                'title' => 'Daily Current Affairs Quiz — September 18, 2026',
                'instructions' => "1. This quiz contains 5 questions calibrated to UPSC Civil Services Prelims standard.\n2. +2.0 marks for each correct response, -0.66 marks for each incorrect response.\n3. Timer will auto-submit when remaining time reaches 0.\n4. Use question palette to jump or mark questions for review.",
                'duration_minutes' => 10,
                'total_marks' => 10.00,
                'marks_per_question' => 2.00,
                'negative_marks_per_question' => 0.66,
                'pass_percentage' => 40.00,
                'is_free' => true,
                'allow_reattempt' => true,
                'status' => 'published',
                'published_at' => now(),
                'created_by' => $adminUser?->id,
            ]
        );

        foreach ($questionsData as $index => $qItem) {
            $question = Question::create([
                'language_id' => $langEn->id,
                'stem' => $qItem['stem'],
                'explanation' => $qItem['explanation'],
                'subject_tag' => $qItem['subject'],
                'difficulty' => 'medium',
                'default_marks' => 2.00,
                'default_negative_marks' => 0.66,
            ]);

            foreach ($qItem['options'] as $oIdx => $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct' => $opt['is_correct'],
                    'sort_order' => $oIdx + 1,
                ]);
            }

            $quizEn->questions()->attach($question->id, [
                'section_name' => 'General Studies Paper I',
                'sort_order' => $index + 1,
            ]);
        }

        // 4. Create Courses & Batches for LMS (Phase 3)
        $courseGs = Course::firstOrCreate(
            ['slug' => 'upsc-cse-foundation-pre-cum-mains-2027'],
            [
                'language_id' => $langEn->id,
                'title' => 'UPSC CSE 2026-27: Comprehensive Foundation Pre-cum-Mains Programme',
                'excerpt' => 'Rigorous 14-month complete syllabus coverage covering GS Papers I to IV, Essay, CSAT, and Daily Answer Writing Practice.',
                'description' => 'Complete classroom & online foundation batch featuring top faculties, weekly tests, evaluated answer copies, and personal academic mentorship.',
                'target_exam' => 'upsc_cse',
                'level' => 'foundation',
                'price' => 85000.00,
                'mrp' => 110000.00,
                'validity_days' => 730,
                'is_featured' => true,
                'status' => 'published',
            ]
        );

        $batchOnline = CourseBatch::firstOrCreate(
            ['batch_code' => 'FOUNDATION-2027-LIVE'],
            [
                'course_id' => $courseGs->id,
                'title' => 'Foundation Batch 2026-27 (Live Interactive + Recorded)',
                'delivery_mode' => 'online',
                'centre_name' => 'Delhi - Karol Bagh & Online Studio',
                'start_date' => now()->addDays(5),
                'class_timing' => '08:30 AM - 11:00 AM (Mon-Fri)',
                'faculty_names' => 'Dr. A. Sharma (Polity), Prof. V. Verma (History), S. Iyer (Economy)',
                'is_enrolment_open' => true,
            ]
        );

        // Seed 4 structured lessons
        $lessonsData = [
            [
                'title' => 'Lecture 01: Indian Constitution — Historical Underpinnings & Preamble Philosophy',
                'duration' => 120,
                'video_url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
                'notes_url' => '/downloads/polity-lecture-01-notes.pdf',
            ],
            [
                'title' => 'Lecture 02: Fundamental Rights & Judicial Review (Articles 12 to 21)',
                'duration' => 115,
                'video_url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
                'notes_url' => '/downloads/polity-lecture-02-notes.pdf',
            ],
            [
                'title' => 'Lecture 03: Directive Principles of State Policy & Fundamental Duties',
                'duration' => 105,
                'video_url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
                'notes_url' => '/downloads/polity-lecture-03-notes.pdf',
            ],
            [
                'title' => 'Lecture 04: Federal Structure & Centre-State Relations (Legislative & Financial)',
                'duration' => 130,
                'video_url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
                'notes_url' => '/downloads/polity-lecture-04-notes.pdf',
            ],
        ];

        foreach ($lessonsData as $lIndex => $les) {
            CourseLesson::firstOrCreate(
                ['course_batch_id' => $batchOnline->id, 'title' => $les['title']],
                [
                    'lesson_type' => 'video',
                    'video_url' => $les['video_url'],
                    'notes_pdf_url' => $les['notes_url'],
                    'duration_minutes' => $les['duration'],
                    'sort_order' => $lIndex + 1,
                    'is_free_preview' => $lIndex === 0,
                ]
            );
        }

        // 5. Enroll the Demo Student User
        if ($studentUser) {
            Enrollment::firstOrCreate(
                [
                    'user_id' => $studentUser->id,
                    'course_id' => $courseGs->id,
                    'course_batch_id' => $batchOnline->id,
                ],
                [
                    'status' => 'active',
                    'enrolled_at' => now()->subDays(10),
                    'expires_at' => now()->addYear(),
                ]
            );

            // Complete a sample quiz attempt for the student
            $scoringService = app(QuizScoringService::class);
            $attempt = $scoringService->startAttempt($studentUser, $quizEn);

            // Answer 4 questions correctly, 1 incorrectly
            $quizQuestions = $quizEn->questions()->with('options')->get();
            foreach ($quizQuestions as $idx => $q) {
                if ($idx < 4) {
                    $correctOpt = $q->options->where('is_correct', true)->first();
                    $scoringService->autosaveAnswer($attempt, $q->id, [$correctOpt->id], false, 45);
                } else {
                    $wrongOpt = $q->options->where('is_correct', false)->first();
                    $scoringService->autosaveAnswer($attempt, $q->id, [$wrongOpt->id], true, 60);
                }
            }

            $scoringService->submitAttempt($attempt);
        }
    }
}
