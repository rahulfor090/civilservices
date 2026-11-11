<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$routesToTest = [
    '/',
    '/courses',
    '/daily-updates',
];

foreach ($routesToTest as $uri) {
    $request = Illuminate\Http\Request::create($uri, 'GET');
    $response = $kernel->handle($request);
    echo "URI: {$uri} => Status: " . $response->getStatusCode() . "\n";
    $kernel->terminate($request, $response);
}
