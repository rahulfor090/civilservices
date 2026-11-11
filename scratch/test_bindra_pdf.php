<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Dompdf\Dompdf;
use Dompdf\Options;

// Create dummy article with exact structure from the screenshot
$article = new App\Models\Article([
    'title' => "India's Sports Governance Revolution: Abhinav Bindra Task Force, Professional Sports Administration and Olympic Vision 2036",
    'excerpt' => "India's sports governance is undergoing an important transformation through the Abhinav Bindra-led Task Force on Capacity Building of Sports Administrators under Khelo Bharat NITI 2025. This UPSC-focused analysis examines the proposed National Council for Sports Education, e-Khel Pathshala, Unique Administrator ID, athlete leadership pathways, digital governance, National Sports Governance Rules 2026 and India's ambition to emerge as a top sporting nation by 2036.",
    'published_at' => now(),
    'reading_minutes' => 5,
    'source_name' => 'Department of Sports & PIB',
]);

// Build sections like in the screenshots
$sampleHtml = '
<h2>1. Why in the News?</h2>
<p>The Government constituted a Task Force on Capacity Building of Sports Administrators under Khelo Bharat NITI 2025, which submitted its report to the Department of Sports.</p>
<p>The Task Force was chaired by Abhinav Bindra, India\'s Olympic gold medallist in shooting.</p>

<div class="callout-box">
    <strong>India\'s Sports Vision 2036</strong>
    <ul>
        <li>Become a top-ten sporting nation by 2036.</li>
        <li>Strengthen the sports governance ecosystem.</li>
        <li>Build professional sports administration capacity.</li>
        <li>Support India\'s ambition to host the 2036 Olympic Games.</li>
    </ul>
</div>

<h2>2. What Is Sports Administration?</h2>
<p>Sports administration refers to the management, regulation, planning and governance of sporting institutions, competitions, athletes, infrastructure and development programmes.</p>

<div class="highlight-box">
    <strong>UPSC Governance Link:</strong> Sports administration demonstrates the importance of institutional capacity, transparency, accountability, professional competence and citizen-centred service delivery.
</div>

<h2>3. Key Provisions of National Sports Governance Rules, 2026</h2>
<table class="content-table">
    <thead>
        <tr>
            <th>Aspect</th>
            <th>Provision Described in the Material</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Institutional structure</strong></td>
            <td>National sports bodies to have a General Body and an Executive Committee.</td>
        </tr>
        <tr>
            <td><strong>Sportsperson representation</strong></td>
            <td>At least four Sportspersons of Outstanding Merit in the General Body.</td>
        </tr>
        <tr>
            <td><strong>Minimum age</strong></td>
            <td>Specified sportsperson representatives must be at least 25 years old.</td>
        </tr>
        <tr>
            <td><strong>Women\'s representation</strong></td>
            <td>At least 50% of the specified sportsperson representatives to be women.</td>
        </tr>
    </tbody>
</table>

<h2>4. UPSC Prelims Practice MCQs</h2>
<div class="question-block">
    <div class="question-stem">Q1. Who chaired the Task Force on Capacity Building of Sports Administrators?</div>
    <div class="question-option">A. Neeraj Chopra</div>
    <div class="question-option">B. Abhinav Bindra</div>
    <div class="question-option">C. P. V. Sindhu</div>
    <div class="question-option">D. Leander Paes</div>
    <div class="question-answer-box">
        <strong>Correct Answer: (B)</strong>
        <div>The Task Force was chaired by Olympic gold medallist Abhinav Bindra.</div>
    </div>
</div>
';

$article->body_html = $sampleHtml;

// Render view
$viewHtml = view('public.articles.pdf', compact('article'))->render();
echo "View rendered successfully. Length: " . strlen($viewHtml) . "\n";
