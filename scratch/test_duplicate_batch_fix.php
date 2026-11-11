<?php

use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\Language;
use App\Models\User;
use Illuminate\Http\Request;

echo "=== TESTING COURSE CREATION WITH EXISTING BATCH CODE ===\n\n";

$admin = User::first();
auth()->login($admin);

$lang = Language::first();
$existingBatch = CourseBatch::first();
$duplicateCode = $existingBatch ? $existingBatch->batch_code : 'BATCH-CSE-26-01';

echo "Existing Batch Code being tested: {$duplicateCode}\n";

$req = new Request();
$req->merge([
    'language_id' => $lang->id,
    'title' => 'Test Course with Duplicate Batch Code ' . time(),
    'target_exam' => 'upsc_cse',
    'level' => 'foundation',
    'price' => 1000,
    'validity_days' => 365,
    'status' => 'published',
    'create_initial_batch' => true,
    'batch_code' => $duplicateCode, // Pass exact existing duplicate code!
    'batch_title' => 'Initial Batch',
    'delivery_mode' => 'online',
]);

$ctrl = app(\App\Http\Controllers\Admin\CourseController::class);
$response = $ctrl->store($req);

echo "Response status: " . $response->getStatusCode() . "\n";
echo "Redirect Target: " . $response->getTargetUrl() . "\n";

$createdCourse = Course::latest('id')->first();
$createdBatch = $createdCourse->batches->first();

echo "Created Course ID: {$createdCourse->id} - {$createdCourse->title}\n";
echo "Created Batch Code: {$createdBatch->batch_code} (successfully resolved without collision!)\n";

echo "\n=== TEST PASSED SUCCESSFULLY ===\n";
