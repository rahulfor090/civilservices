<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$testUrls = [
    'https://www.youtube.com/',
    'https://www.youtube.com',
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    'https://youtu.be/dQw4w9WgXcQ',
    'https://www.youtube.com/embed/dQw4w9WgXcQ',
    'https://www.youtube.com/live/dQw4w9WgXcQ',
    'https://www.youtube.com/shorts/dQw4w9WgXcQ',
    'https://vimeo.com/123456789',
];

function getEmbedUrl(?string $url): ?string {
    if (empty($url)) return null;
    $url = trim($url);
    if (preg_match('#(?:youtube\.com/(?:watch\?v=|embed/|live/|shorts/)|youtu\.be/)([a-zA-Z0-9_\-]{6,15})#i', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
    }
    if (preg_match('#^https?://(www\.)?youtube\.com/?$#i', $url)) {
        return null;
    }
    if (preg_match('#vimeo\.com/([0-9]+)#i', $url, $m)) {
        return 'https://player.vimeo.com/video/' . $m[1];
    }
    return $url;
}

foreach ($testUrls as $u) {
    echo $u . "\n  => " . (getEmbedUrl($u) ?? '[NULL - invalid embed]') . "\n";
}
