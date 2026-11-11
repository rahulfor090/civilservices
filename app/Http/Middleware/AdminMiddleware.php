<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->guest(route('login'))->with('error', 'Please sign in to access the staff portal.');
        }

        $user = auth()->user();

        // Check if user has administrative or staff role
        $staffRoles = [
            'super_admin',
            'senior_editor',
            'editor',
            'quiz_author',
            'evaluator',
            'sales_counsellor',
            'finance',
            'centre_manager',
            'support_agent'
        ];

        $hasStaffRole = false;
        foreach ($staffRoles as $role) {
            if ($user->hasRole($role)) {
                $hasStaffRole = true;
                break;
            }
        }

        if (!$hasStaffRole) {
            abort(403, 'Unauthorized. Staff privileges required.');
        }

        return $next($request);
    }
}
