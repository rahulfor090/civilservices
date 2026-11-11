<?php

namespace Database\Seeders;

use App\Models\Centre;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\Language;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\MainsEvaluationScore;
use App\Models\MainsSubmission;
use App\Models\MainsTest;
use App\Models\MainsTestQuestion;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CentreAndMainsSeeder extends Seeder
{
    public function run(): void
    {
        $en = Language::where('code', 'en')->first() ?? Language::first();
        $hi = Language::where('code', 'hi')->first() ?? $en;

        // 1. Seed All 11 Verified Physical National Centres
        $centresData = [
            [
                'name' => 'Delhi Old Rajinder Nagar (National Headquarters)',
                'slug' => 'delhi-old-rajinder-nagar',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'address' => '57, 16, Block 47, Old Rajinder Nagar, Rajinder Nagar, Delhi, 110060',
                'pincode' => '110060',
                'phone' => '+91 9911020626',
                'email' => 'info@civilservicegurukul.com',
                'google_maps_url' => 'https://maps.google.com/?q=57+16+Block+47+Old+Rajinder+Nagar+Delhi+110060',
                'latitude' => 28.6387000,
                'longitude' => 77.1852000,
                'facilities' => ['Smart AC Classrooms', '24x7 Modern Study Library', 'Offline Doubt Counters', 'UPSC Prelims Mock Hall', '1-on-1 Faculty Mentorship'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Delhi Mukherjee Nagar Campus',
                'slug' => 'delhi-mukherjee-nagar',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'address' => 'Vardhman Commercial Complex, Near Batra Cinema, Mukherjee Nagar, Delhi',
                'pincode' => '110009',
                'phone' => '+91-11-47532223',
                'email' => 'mukherjeenagar@civilservicegurukul.com',
                'google_maps_url' => 'https://maps.google.com/?q=Mukherjee+Nagar+Batra+Cinema+Delhi',
                'latitude' => 28.7075000,
                'longitude' => 77.2142000,
                'facilities' => ['Bilingual Hindi & English Classrooms', 'Study Library', 'Faculty Lounge', 'Interview Room'],
                'sort_order' => 2,
            ],
            [
                'name' => 'Noida Campus',
                'slug' => 'noida',
                'city' => 'Noida',
                'state' => 'Uttar Pradesh',
                'address' => 'B-37, Sector 62, Institutional Area, Near Electronic City Metro, Noida',
                'pincode' => '201309',
                'phone' => '+91-120-4567890',
                'email' => 'noida@civilservicegurukul.com',
                'google_maps_url' => 'https://maps.google.com/?q=Noida+Sector+62',
                'latitude' => 28.6280000,
                'longitude' => 77.3649000,
                'facilities' => ['Smart Classrooms', 'Reading Room', 'Career Counselling Lounge'],
                'sort_order' => 3,
            ],
            [
                'name' => 'Prayagraj Campus',
                'slug' => 'prayagraj',
                'city' => 'Prayagraj',
                'state' => 'Uttar Pradesh',
                'address' => '10/2, Tashkent Marg, Near Patrika Chauraha, Civil Lines, Prayagraj',
                'pincode' => '211001',
                'phone' => '+91-532-2423333',
                'email' => 'prayagraj@civilservicegurukul.com',
                'google_maps_url' => 'https://maps.google.com/?q=Tashkent+Marg+Prayagraj',
                'latitude' => 25.4529000,
                'longitude' => 81.8349000,
                'facilities' => ['UPPSC & UPSC Dedicated Wings', 'Library', 'Hostel Tie-ups'],
                'sort_order' => 4,
            ],
            [
                'name' => 'Jaipur Campus',
                'slug' => 'jaipur',
                'city' => 'Jaipur',
                'state' => 'Rajasthan',
                'address' => 'Plot No. 45, Gopalpura Bypass Road, Near Ridhi Sidhi Chauraha, Tonk Road, Jaipur',
                'pincode' => '302018',
                'phone' => '+91-141-2781111',
                'email' => 'jaipur@civilservicegurukul.com',
                'google_maps_url' => 'https://maps.google.com/?q=Gopalpura+Bypass+Jaipur',
                'latitude' => 26.8528000,
                'longitude' => 75.7891000,
                'facilities' => ['RAS & UPSC Combined Classrooms', 'Air-Conditioned Library', 'Doubt Counters'],
                'sort_order' => 5,
            ],
            [
                'name' => 'Patna Campus',
                'slug' => 'patna',
                'city' => 'Patna',
                'state' => 'Bihar',
                'address' => '3rd Floor, Grand Plaza, Fraser Road / Boring Canal Road, Anandpuri, Patna',
                'pincode' => '800001',
                'phone' => '+91-612-2554444',
                'email' => 'patna@civilservicegurukul.com',
                'google_maps_url' => 'https://maps.google.com/?q=Boring+Road+Patna',
                'latitude' => 25.6108000,
                'longitude' => 85.1189000,
                'facilities' => ['BPSC & UPSC Batches', 'Reference Library', 'Interview Panel Chambers'],
                'sort_order' => 6,
            ],
            [
                'name' => 'Lucknow Campus',
                'slug' => 'lucknow',
                'city' => 'Lucknow',
                'state' => 'Uttar Pradesh',
                'address' => 'B-24, Kapoorthala Commercial Complex, Aliganj, Lucknow',
                'pincode' => '226024',
                'phone' => '+91-522-4008888',
                'email' => 'lucknow@civilservicegurukul.com',
                'google_maps_url' => 'https://maps.google.com/?q=Kapoorthala+Aliganj+Lucknow',
                'latitude' => 26.8833000,
                'longitude' => 80.9462000,
                'facilities' => ['UP Special Modules', 'Smart Projection Classrooms', 'Study Cabins'],
                'sort_order' => 7,
            ],
            [
                'name' => 'Indore Campus',
                'slug' => 'indore',
                'city' => 'Indore',
                'state' => 'Madhya Pradesh',
                'address' => '102, Tower Square, Bhawarkua Main Road, Near Vishnu Puri, Indore',
                'pincode' => '452001',
                'phone' => '+91-731-4209999',
                'email' => 'indore@civilservicegurukul.com',
                'google_maps_url' => 'https://maps.google.com/?q=Bhawarkua+Indore',
                'latitude' => 22.6926000,
                'longitude' => 75.8676000,
                'facilities' => ['MPPSC & UPSC Classrooms', 'Digital Video Archive', '24x7 Reading Lounge'],
                'sort_order' => 8,
            ],
            [
                'name' => 'Ranchi Campus',
                'slug' => 'ranchi',
                'city' => 'Ranchi',
                'state' => 'Jharkhand',
                'address' => 'Hari Om Tower, 4th Floor, Circular Road, Lalpur, Ranchi',
                'pincode' => '834001',
                'phone' => '+91-651-2331111',
                'email' => 'ranchi@civilservicegurukul.com',
                'google_maps_url' => 'https://maps.google.com/?q=Circular+Road+Ranchi',
                'latitude' => 23.3683000,
                'longitude' => 85.3341000,
                'facilities' => ['JPSC & UPSC Modules', 'Mains Writing Hall', 'Counselling Chambers'],
                'sort_order' => 9,
            ],
            [
                'name' => 'Agra Campus',
                'slug' => 'agra',
                'city' => 'Agra',
                'state' => 'Uttar Pradesh',
                'address' => 'Block 18, Commercial Belt, Sanjay Place, Agra',
                'pincode' => '282002',
                'phone' => '+91-562-2856666',
                'email' => 'agra@civilservicegurukul.com',
                'google_maps_url' => 'https://maps.google.com/?q=Sanjay+Place+Agra',
                'latitude' => 27.2023000,
                'longitude' => 78.0069000,
                'facilities' => ['Classrooms', 'Library', 'Student Mentorship Lounge'],
                'sort_order' => 10,
            ],
            [
                'name' => 'Pune Campus',
                'slug' => 'pune',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'address' => 'Ghole Road, Off Fergusson College Road, Shivajinagar, Pune',
                'pincode' => '411005',
                'phone' => '+91-20-25537777',
                'email' => 'pune@civilservicegurukul.com',
                'google_maps_url' => 'https://maps.google.com/?q=FC+Road+Shivajinagar+Pune',
                'latitude' => 18.5284000,
                'longitude' => 73.8438000,
                'facilities' => ['MPSC & UPSC Dual Streams', 'AC Classrooms', 'Mock Interview Board'],
                'sort_order' => 11,
            ],
        ];

        $headquarters = null;
        foreach ($centresData as $cData) {
            $centre = Centre::updateOrCreate(['slug' => $cData['slug']], $cData);
            if ($centre->slug === 'delhi-old-rajinder-nagar') {
                $headquarters = $centre;
            }
        }

        // Link a batch to Headquarters if a course exists
        $course = Course::first();
        if ($course && $headquarters) {
            CourseBatch::firstOrCreate(
                ['batch_code' => 'DEL-ORN-FND-2026'],
                [
                    'course_id' => $course->id,
                    'centre_id' => $headquarters->id,
                    'title' => 'Delhi Old Rajinder Nagar Classroom Foundation 2026',
                    'delivery_mode' => 'offline',
                    'centre_name' => 'Old Rajinder Nagar, Delhi',
                    'start_date' => now()->addDays(14)->toDateString(),
                    'class_timing' => '08:30 AM - 11:00 AM',
                    'faculty_names' => 'Senior GS Faculty Team (Delhi)',
                ]
            );
        }

        // 2. Evaluator & Counsellor Staff Users
        $evaluatorRole = Role::where('slug', 'evaluator')->first() ?? Role::where('slug', 'editor')->first();
        $counsellorRole = Role::where('slug', 'sales_counsellor')->first() ?? Role::where('slug', 'editor')->first();

        $evaluator = User::firstOrCreate(
            ['email' => 'evaluator@civilservicegurukul.com'],
            [
                'name' => 'Dr. K. S. Verma (Senior Mains Evaluator)',
                'password' => Hash::make('password123'),
                'mobile' => '9811002233',
                'country_code' => '+91',
                'status' => 'active',
            ]
        );
        if ($evaluatorRole && !$evaluator->roles->contains($evaluatorRole->id)) {
            $evaluator->roles()->attach($evaluatorRole->id);
        }

        $counsellor = User::firstOrCreate(
            ['email' => 'counsellor@civilservicegurukul.com'],
            [
                'name' => 'Pooja Sharma (Senior Academic Counsellor)',
                'password' => Hash::make('password123'),
                'mobile' => '9811004455',
                'country_code' => '+91',
                'status' => 'active',
            ]
        );
        if ($counsellorRole && !$counsellor->roles->contains($counsellorRole->id)) {
            $counsellor->roles()->attach($counsellorRole->id);
        }

        // 3. Seed Mains Subjective Tests
        $tests = [
            [
                'title' => 'UPSC Mains GS Paper 1 Mock Test — History & Geography',
                'slug' => 'upsc-mains-gs1-mock-2026',
                'language_id' => $en->id,
                'gs_paper' => 'gs1',
                'total_marks' => 50,
                'duration_minutes' => 60,
                'instructions' => 'There are 3 questions. Question 1 carries 10 marks (150 words). Questions 2 and 3 carry 20 marks each (250 words). Content is more important than length.',
                'status' => 'active',
                'questions' => [
                    [
                        'question_number' => 1,
                        'stem' => 'Examine the significance of the Bhakti movement in fostering cultural integration and regional languages in medieval India. (150 words)',
                        'max_marks' => 10.0,
                        'word_limit' => 150,
                    ],
                    [
                        'question_number' => 2,
                        'stem' => 'Discuss the causes of depletion of mangroves in India and evaluate the importance of the MISHTI scheme in coastal resilience. (250 words)',
                        'max_marks' => 20.0,
                        'word_limit' => 250,
                    ],
                    [
                        'question_number' => 3,
                        'stem' => 'Account for the changing pattern of urbanisation in tier-2 Indian cities and its environmental implications. (250 words)',
                        'max_marks' => 20.0,
                        'word_limit' => 250,
                    ],
                ]
            ],
            [
                'title' => 'UPSC Mains GS Paper 2 Mock Test — Polity & Governance',
                'slug' => 'upsc-mains-gs2-mock-2026',
                'language_id' => $en->id,
                'gs_paper' => 'gs2',
                'total_marks' => 50,
                'duration_minutes' => 60,
                'instructions' => 'Attempt all questions in order. Substantiate arguments with relevant constitutional articles, Supreme Court judgements, and Law Commission reports.',
                'status' => 'active',
                'questions' => [
                    [
                        'question_number' => 1,
                        'stem' => 'Judicial Review is the cornerstone of constitutionalism in India. Analyse in the light of the Basic Structure doctrine. (150 words)',
                        'max_marks' => 10.0,
                        'word_limit' => 150,
                    ],
                    [
                        'question_number' => 2,
                        'stem' => 'The institution of the Governor has increasingly become a flashpoint in Centre-State relations. Discuss reforms suggested by the Sarkaria and Punchhi commissions. (250 words)',
                        'max_marks' => 20.0,
                        'word_limit' => 250,
                    ],
                    [
                        'question_number' => 3,
                        'stem' => 'India\'s leadership in the Global South is anchored in multilateralism and digital public infrastructure. Critically evaluate. (250 words)',
                        'max_marks' => 20.0,
                        'word_limit' => 250,
                    ],
                ]
            ],
            [
                'title' => 'UPSC Mains GS Paper 3 Mock Test — Economy, Tech & Security',
                'slug' => 'upsc-mains-gs3-mock-2026',
                'language_id' => $en->id,
                'gs_paper' => 'gs3',
                'total_marks' => 50,
                'duration_minutes' => 60,
                'instructions' => 'Address both economic and technical dimensions clearly with recent budgetary and Economic Survey figures.',
                'status' => 'active',
                'questions' => [
                    [
                        'question_number' => 1,
                        'stem' => 'How can renewable energy storage systems resolve grid instability as India approaches 500 GW non-fossil capacity by 2030? (150 words)',
                        'max_marks' => 10.0,
                        'word_limit' => 150,
                    ],
                    [
                        'question_number' => 2,
                        'stem' => 'Cross-border digital financial fraud poses an asymmetric challenge to national internal security. Discuss cybersecurity preparedness. (250 words)',
                        'max_marks' => 20.0,
                        'word_limit' => 250,
                    ],
                    [
                        'question_number' => 3,
                        'stem' => 'Evaluate the impact of Direct Benefit Transfers (DBT) via JAM trinity on fiscal deficit and leakage elimination. (250 words)',
                        'max_marks' => 20.0,
                        'word_limit' => 250,
                    ],
                ]
            ],
            [
                'title' => 'UPSC Mains GS Paper 4 Mock Test — Ethics & Case Studies',
                'slug' => 'upsc-mains-gs4-mock-2026',
                'language_id' => $en->id,
                'gs_paper' => 'gs4',
                'total_marks' => 50,
                'duration_minutes' => 60,
                'instructions' => 'Apply ethical frameworks (Deontology, Consequentialism, Virtue Ethics) and demonstrate emotional intelligence in civil service conduct.',
                'status' => 'active',
                'questions' => [
                    [
                        'question_number' => 1,
                        'stem' => 'What does "probity in governance" mean to you as a civil servant? Differentiate probity from mere compliance with the law. (150 words)',
                        'max_marks' => 10.0,
                        'word_limit' => 150,
                    ],
                    [
                        'question_number' => 2,
                        'stem' => 'Case Study: You are a District Magistrate tasked with rehabilitating encroachers on river floodplains. Influential builders and local communities resist relocation. Formulate an ethical and administrative action plan. (250 words)',
                        'max_marks' => 20.0,
                        'word_limit' => 250,
                    ],
                    [
                        'question_number' => 3,
                        'stem' => 'Emotional intelligence is more important than IQ for an administrator resolving public agitations. Discuss with real-life administrative examples. (250 words)',
                        'max_marks' => 20.0,
                        'word_limit' => 250,
                    ],
                ]
            ],
        ];

        $student = User::where('email', 'student@civilservicegurukul.com')->first();

        foreach ($tests as $tData) {
            $questionsData = $tData['questions'];
            unset($tData['questions']);

            $mTest = MainsTest::updateOrCreate(['slug' => $tData['slug']], $tData);

            $seededQuestions = [];
            foreach ($questionsData as $qData) {
                $qData['mains_test_id'] = $mTest->id;
                $seededQuestions[] = MainsTestQuestion::updateOrCreate(
                    ['mains_test_id' => $mTest->id, 'question_number' => $qData['question_number']],
                    $qData
                );
            }

            // Create a graded submission for the student on GS 1 test
            if ($student && $mTest->slug === 'upsc-mains-gs1-mock-2026') {
                $sub = MainsSubmission::updateOrCreate(
                    ['user_id' => $student->id, 'mains_test_id' => $mTest->id],
                    [
                        'candidate_roll_no' => 'UPSC-849201',
                        'submitted_copy_pdf' => 'mains_copies/sample_gs1_copy.pdf',
                        'evaluated_copy_pdf' => 'evaluated_copies/sample_gs1_evaluated.pdf',
                        'evaluator_id' => $evaluator->id,
                        'status' => 'evaluated',
                        'total_score' => 31.5,
                        'evaluator_remarks' => 'Commendable effort! Strong introduction in Question 1 on Bhakti movement. In Question 2, cite the MISHTI budget allocation and add a coastal schematic diagram for higher marks.',
                        'submitted_at' => now()->subDays(2),
                        'evaluated_at' => now()->subDay(),
                    ]
                );

                // Add scores for the 3 questions
                MainsEvaluationScore::updateOrCreate(
                    ['mains_submission_id' => $sub->id, 'mains_test_question_id' => $seededQuestions[0]->id],
                    [
                        'intro_score' => 2.0,
                        'content_score' => 3.5,
                        'structure_score' => 1.0,
                        'conclusion_score' => 0.5,
                        'total_question_score' => 7.0,
                        'evaluator_comments' => 'Well-balanced answer mentioning Kabir, Mirabai and regional Bhakti literature.',
                    ]
                );

                MainsEvaluationScore::updateOrCreate(
                    ['mains_submission_id' => $sub->id, 'mains_test_question_id' => $seededQuestions[1]->id],
                    [
                        'intro_score' => 2.5,
                        'content_score' => 6.5,
                        'structure_score' => 2.0,
                        'conclusion_score' => 1.5,
                        'total_question_score' => 12.5,
                        'evaluator_comments' => 'Good coverage of mangrove ecological services; integrate Sundarbans and Pichavaram examples.',
                    ]
                );

                MainsEvaluationScore::updateOrCreate(
                    ['mains_submission_id' => $sub->id, 'mains_test_question_id' => $seededQuestions[2]->id],
                    [
                        'intro_score' => 2.5,
                        'content_score' => 6.0,
                        'structure_score' => 2.0,
                        'conclusion_score' => 1.5,
                        'total_question_score' => 12.0,
                        'evaluator_comments' => 'Urban sprawl and heat islands well explained.',
                    ]
                );
            }
        }

        // 4. Seed Diverse Sample Leads into CRM
        $leads = [
            [
                'name' => 'Aditya Kashyap',
                'mobile' => '9876501122',
                'email' => 'aditya.kashyap@gmail.com',
                'centre_id' => $headquarters?->id,
                'interest' => 'UPSC Foundation (Pre-cum-Mains)',
                'source' => 'counselling_booking',
                'status' => 'new',
                'assigned_to' => $counsellor->id,
                'follow_up_at' => now()->addDays(1),
            ],
            [
                'name' => 'Meera Nambiar',
                'mobile' => '9876503344',
                'email' => 'meera.nambiar@gmail.com',
                'centre_id' => $headquarters?->id,
                'interest' => 'Interview Guidance Programme (IGP)',
                'source' => 'interview_guidance',
                'status' => 'counselling_scheduled',
                'assigned_to' => $counsellor->id,
                'follow_up_at' => now()->addHours(6),
            ],
            [
                'name' => 'Rohan Deshmukh',
                'mobile' => '9876505566',
                'email' => 'rohan.deshmukh@gmail.com',
                'centre_id' => null,
                'interest' => 'Daily Mains Answer Writing Programme',
                'source' => 'download_gate',
                'status' => 'contacted',
                'assigned_to' => $counsellor->id,
            ],
            [
                'name' => 'Ananya Sen',
                'mobile' => '9876507788',
                'email' => 'ananya.sen@gmail.com',
                'centre_id' => $headquarters?->id,
                'interest' => 'GS Foundation 2026-27',
                'source' => 'homepage_counselling',
                'status' => 'won',
                'assigned_to' => $counsellor->id,
            ],
        ];

        foreach ($leads as $lData) {
            $lead = Lead::updateOrCreate(['mobile' => $lData['mobile']], $lData);
            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => $counsellor->id,
                'type' => 'status_change',
                'outcome' => 'Lead entered CRM system',
                'notes' => "Candidate interested in {$lead->interest} for target 2026.",
                'created_at' => now(),
            ]);
        }
    }
}
