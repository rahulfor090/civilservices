<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Detect Hindi prefix in path or query parameter
        if ($request->is('hindi') || $request->is('hindi/*') || $request->query('lang') === 'hi') {
            App::setLocale('hi');
            Carbon::setLocale('hi');
        } else {
            App::setLocale('en');
            Carbon::setLocale('en');
        }

        return $next($request);
    }
}
