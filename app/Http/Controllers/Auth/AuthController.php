<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }
            $user = Auth::user();
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            AuditLog::record($user, 'auth', 'login_success', null, null, ['ip' => $request->ip()]);

            if ($user->hasRole('super_admin') || $user->hasRole('senior_editor') || $user->hasRole('editor')) {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        if ($user = Auth::user()) {
            AuditLog::record($user, 'auth', 'logout', null, null, ['ip' => $request->ip()]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showRegister(Request $request): View
    {
        return view('auth.register', [
            'redirect' => $request->query('redirect'),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'mobile' => ['required', 'string', 'regex:/^[0-9]{10}$/', 'unique:users,mobile'],
            'target_exam' => 'required|string|in:upsc_cse,uppcs,bpsc,ras',
            'locale' => 'required|string|in:en,hi',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'country_code' => '+91',
            'password' => Hash::make($validated['password']),
            'target_exam' => $validated['target_exam'],
            'locale' => $validated['locale'],
            'preparation_mode' => 'online',
            'status' => 'active',
            'marketing_consent' => true,
            'consent_at' => now(),
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $role = \App\Models\Role::where('slug', 'registered_student')->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        AuditLog::record($user, 'auth', 'register', $user, null, ['ip' => $request->ip(), 'target_exam' => $validated['target_exam']]);

        Auth::login($user);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $redirectUrl = $request->input('redirect');
        if (!empty($redirectUrl)) {
            // Validate local redirect
            $path = parse_url($redirectUrl, PHP_URL_PATH) ?? $redirectUrl;
            if (str_starts_with($path, '/')) {
                return redirect($path)->with('success', 'Welcome to Civil Service Gurukul! Your student account is now active.');
            }
        }

        return redirect()->intended(route('student.dashboard'))->with('success', 'Welcome to Civil Service Gurukul! Your student account is now active.');
    }
}
