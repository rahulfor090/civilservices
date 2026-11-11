<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$article = App\Models\Article::where('slug', '18-sept-upi-update-case-system')->first();
if (!$article) {
    echo "Article not found\n";
    exit(1);
}

// Check or create the specialized quiz for this editorial
$quizTitle = 'Daily Editorial Practice Quiz: UPI Updates, Digital Payments & Fintech Ecosystem (18 Sept 2026)';
$quizSlug = 'daily-editorial-quiz-upi-updates-digital-payments';

$quiz = App\Models\Quiz::firstOrCreate(
    ['slug' => $quizSlug],
    [
        'quiz_category_id' => App\Models\QuizCategory::first()?->id ?? 1,
        'language_id' => $article->language_id ?? 1,
        'title' => $quizTitle,
        'instructions' => 'This daily practice test evaluates your conceptual mastery of UPI architecture, NPCI regulatory framework, and RBI digital payment guidelines discussed in today’s editorial.',
        'duration_minutes' => 10,
        'total_marks' => 10.00,
        'marks_per_question' => 2.00,
        'negative_marks_per_question' => 0.66,
        'pass_percentage' => 40.00,
        'is_free' => true,
        'allow_reattempt' => true,
        'shuffle_questions' => true,
        'status' => 'published',
        'published_at' => now(),
        'created_by' => 1,
    ]
);

// Associate with the article!
$article->update(['quiz_id' => $quiz->id]);
echo "Article #{$article->id} ({$article->title}) associated with Quiz #{$quiz->id} ({$quiz->title})\n";

// Add 5 UPSC Prelims standard questions if not already attached
if ($quiz->questions()->count() === 0) {
    $questionsData = [
        [
            'stem' => 'With reference to the Unified Payments Interface (UPI) in India, consider the following statements:<br>1. UPI was developed by the National Payments Corporation of India (NPCI), an initiative of the Reserve Bank of India (RBI) and Indian Banks’ Association (IBA).<br>2. UPI supports interoperability between different bank accounts and mobile applications through a Single Identifier (Virtual Payment Address).<br>3. RuPay credit cards can now be linked directly to UPI for making peer-to-merchant (P2M) transactions.<br>Which of the statements given above are correct?',
            'explanation' => 'All three statements are correct.<br>Statement 1 is correct: NPCI is an umbrella organization for operating retail payments and settlement systems in India, initiated by RBI and IBA under the Payment and Settlement Systems Act, 2007.<br>Statement 2 is correct: UPI enables instant fund transfer between any two bank accounts on mobile without sharing bank account numbers or IFSC codes, using a Virtual Payment Address (VPA).<br>Statement 3 is correct: RBI has permitted the linking of RuPay credit cards to UPI to enhance credit penetration and digital merchant transactions.',
            'subject_tag' => 'Economy',
            'topic_tag' => 'Digital Payments & Fintech',
            'difficulty' => 'medium',
            'options' => [
                ['option_text' => '1 and 2 only', 'is_correct' => false],
                ['option_text' => '2 and 3 only', 'is_correct' => false],
                ['option_text' => '1 and 3 only', 'is_correct' => false],
                ['option_text' => '1, 2 and 3', 'is_correct' => true],
            ]
        ],
        [
            'stem' => 'Regarding the National Payments Corporation of India (NPCI), consider the following statements:<br>1. It is registered as a "Not for Profit" company under Section 8 of the Companies Act 2013.<br>2. It operates the Bharat Interface for Money (BHIM) and National Electronic Toll Collection (FASTag).<br>Which of the statements given above is/are correct?',
            'explanation' => 'Both statements 1 and 2 are correct.<br>Statement 1 is correct: NPCI is incorporated as a not-for-profit company under Section 8 of the Companies Act 2013 (earlier Section 25 of the Companies Act 1956) to provide robust payment infrastructure without profit maximization.<br>Statement 2 is correct: NPCI manages key retail settlement platforms including BHIM, Immediate Payment Service (IMPS), National Financial Switch (NFS), RuPay, FASTag, and UPI.',
            'subject_tag' => 'Economy',
            'topic_tag' => 'NPCI & Financial Inclusion',
            'difficulty' => 'easy',
            'options' => [
                ['option_text' => '1 only', 'is_correct' => false],
                ['option_text' => '2 only', 'is_correct' => false],
                ['option_text' => 'Both 1 and 2', 'is_correct' => true],
                ['option_text' => 'Neither 1 nor 2', 'is_correct' => false],
            ]
        ],
        [
            'stem' => 'With reference to the Central Bank Digital Currency (CBDC / Digital Rupee) and Unified Payments Interface (UPI), consider the following statements:<br>1. While UPI is a payment mechanism/rail, the e-Rupee is legal tender sovereign currency itself.<br>2. RBI has made the digital rupee QR codes interoperable with UPI QR codes for merchant transactions.<br>Which of the statements given above is/are correct?',
            'explanation' => 'Both statements 1 and 2 are correct.<br>Statement 1 is correct: UPI is an instant payment infrastructure/rail that facilitates movement of commercial bank money from one account to another. In contrast, CBDC (e-Rupee) is a direct liability of the RBI (sovereign legal tender) and represents digital cash.<br>Statement 2 is correct: RBI introduced full interoperability allowing users to scan existing UPI QR codes with their Digital Rupee wallet app to make payments directly using e-Rupee.',
            'subject_tag' => 'Economy',
            'topic_tag' => 'Monetary Policy & CBDC',
            'difficulty' => 'medium',
            'options' => [
                ['option_text' => '1 only', 'is_correct' => false],
                ['option_text' => '2 only', 'is_correct' => false],
                ['option_text' => 'Both 1 and 2', 'is_correct' => true],
                ['option_text' => 'Neither 1 nor 2', 'is_correct' => false],
            ]
        ],
        [
            'stem' => 'Consider the following international linkages of India’s Unified Payments Interface (UPI):<br>1. Singapore – PayNow<br>2. UAE – Jaywan / Instant Payment Platform<br>3. France – Eiffel Tower & Lyra Network<br>In which of the countries listed above can Indian tourists or residents use UPI mechanisms for cross-border remittances or merchant payments?',
            'explanation' => 'All three countries have operationalized UPI linkages.<br>1. Singapore: Cross-border linkage between India’s UPI and Singapore’s PayNow facilitates real-time low-cost cross-border remittances.<br>2. UAE: NPCI International Payments Limited (NIPL) tied up with UAE’s central bank network to enable UPI payments for merchants and tourists.<br>3. France: UPI acceptance was launched at key destinations like the Eiffel Tower and Parisian merchants via Lyra Network.',
            'subject_tag' => 'International Relations & Economy',
            'topic_tag' => 'Cross-Border Digital Payments',
            'difficulty' => 'medium',
            'options' => [
                ['option_text' => '1 and 2 only', 'is_correct' => false],
                ['option_text' => '2 and 3 only', 'is_correct' => false],
                ['option_text' => '1 and 3 only', 'is_correct' => false],
                ['option_text' => '1, 2 and 3', 'is_correct' => true],
            ]
        ],
        [
            'stem' => 'Under RBI guidelines for payment ecosystem resilience and consumer protection, what is the significance of the "Zero Liability" framework for electronic transactions in case of third-party breach?',
            'explanation' => 'Under RBI’s circular on customer protection in unauthorized electronic transactions, a customer has ZERO liability if the unauthorized transaction occurs due to a contributory fraud/negligence/deficiency on the part of the bank, or a third-party breach where the customer notifies the bank within three working days of receiving the alert.',
            'subject_tag' => 'Governance & Economy',
            'topic_tag' => 'Consumer Protection in Banking',
            'difficulty' => 'hard',
            'options' => [
                ['option_text' => 'Customers must always bear 50% of the loss irrespective of notification time', 'is_correct' => false],
                ['option_text' => 'Customer has zero liability if unauthorized transaction is notified to bank within three working days', 'is_correct' => true],
                ['option_text' => 'Zero liability only applies to corporate accounts, not individual retail accounts', 'is_correct' => false],
                ['option_text' => 'Liability is determined by local police FIR rather than RBI guidelines', 'is_correct' => false],
            ]
        ],
    ];

    $sort = 1;
    foreach ($questionsData as $qData) {
        $question = App\Models\Question::create([
            'language_id' => $quiz->language_id,
            'stem' => $qData['stem'],
            'explanation' => $qData['explanation'],
            'subject_tag' => $qData['subject_tag'],
            'topic_tag' => $qData['topic_tag'],
            'difficulty' => $qData['difficulty'],
            'question_type' => 'single_choice',
            'default_marks' => 2.00,
            'default_negative_marks' => 0.66,
            'is_active' => true,
        ]);

        foreach ($qData['options'] as $idx => $opt) {
            $question->options()->create([
                'option_text' => $opt['option_text'],
                'is_correct' => $opt['is_correct'],
                'sort_order' => $idx + 1,
            ]);
        }

        $quiz->questions()->attach($question->id, [
            'section_name' => 'Economy & Digital Governance',
            'sort_order' => $sort++,
        ]);
    }

    echo "Created and attached 5 UPSC Prelims questions to Quiz #{$quiz->id}!\n";
} else {
    echo "Quiz already has " . $quiz->questions()->count() . " questions.\n";
}
