<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;

echo "Current server now(): " . now()->format('Y-m-d H:i:s T') . "\n";
echo "Current IST now('Asia/Kolkata'): " . now('Asia/Kolkata')->format('Y-m-d H:i:s T') . "\n";

$scheduledDateStr = '2026-10-11 17:00:00';
$unlockIST = Carbon::parse($scheduledDateStr, 'Asia/Kolkata');
$currentIST = now('Asia/Kolkata');

echo "Scheduled IST: " . $unlockIST->format('Y-m-d H:i:s T') . "\n";
echo "Is unlocked right now in IST? " . ($currentIST->greaterThanOrEqualTo($unlockIST) ? 'YES (UNLOCKED!)' : 'NO (STILL LOCKED)') . "\n";
