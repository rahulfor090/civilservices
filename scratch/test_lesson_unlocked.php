<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$lesson = new App\Models\CourseLesson([
    'title' => 'Introduction',
    'scheduled_date' => '2026-10-11',
    'scheduled_time' => '17:00:00',
    'scheduled_at' => '2026-10-11 17:00:00',
    'is_free_preview' => false,
]);

echo "Lesson Title: " . $lesson->title . "\n";
echo "Lesson Scheduled At: " . $lesson->scheduled_at . "\n";
echo "Current App Timezone: " . config('app.timezone') . "\n";
echo "Current Time (now): " . now()->format('Y-m-d H:i:s T') . "\n";
echo "Is Unlocked: " . ($lesson->is_unlocked ? 'YES (UNLOCKED)' : 'NO (LOCKED)') . "\n";
