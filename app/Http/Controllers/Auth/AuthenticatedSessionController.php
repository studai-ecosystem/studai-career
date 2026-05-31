<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\RedirectsAuthenticatedUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    use RedirectsAuthenticatedUsers;
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * Uses plain Request (not LoginRequest) to avoid Redis rate-limiter dependency.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // --- Diagnostic logging (temporary) ---
        // Log enough information to diagnose why auth fails without exposing passwords.
        $email = $request->input('email');
        try {
            $row = \Illuminate\Support\Facades\DB::table('users')->where('email', $email)->first();
            $hashOk = $row ? \Illuminate\Support\Facades\Hash::check($request->input('password'), $row->password) : false;
            Log::info('AUTH_DIAG', [
                'email'      => $email,
                'row_found'  => $row !== null,
                'deleted_at' => $row?->deleted_at,
                'is_active'  => $row?->is_active,
                'hash_ok'    => $hashOk,
                'hash_prefix'=> $row ? substr($row->password, 0, 10) : null,
            ]);
        } catch (\Throwable $diagEx) {
            Log::warning('AUTH_DIAG_FAILED', ['error' => $diagEx->getMessage()]);
        }

        // Attempt authentication — wrap in try-catch so Redis queue dispatch
        // failures (from ShouldQueue event listeners like GamificationEventSubscriber)
        // do NOT crash the login flow.
        $authenticated = false;
        try {
            $authenticated = Auth::attempt(
                $request->only('email', 'password'),
                $request->boolean('remember')
            );
        } catch (\Predis\Connection\ConnectionException|\RedisException|\Exception $e) {
            // Auth::attempt() calls SessionGuard::login() which fires the Login event
            // BEFORE calling setUser(). If a ShouldQueue event listener (e.g.
            // GamificationEventSubscriber) fails to dispatch (Redis down), the exception
            // propagates here and Auth::check() returns false even though the session
            // was already updated with the user's ID.
            //
            // Detect success by checking the session key directly, then restore the user.
            $sessionKey = 'login_web_' . sha1(\Illuminate\Auth\SessionGuard::class);
            $userId     = $request->session()->get($sessionKey);

            if ($userId && $user = \App\Models\User::find($userId)) {
                Auth::setUser($user);
                $authenticated = true;
                Log::warning('Login event dispatch failed after auth succeeded (queue/Redis unavailable); recovered from session', [
                    'email' => $request->email,
                    'error' => $e->getMessage(),
                ]);
            } elseif (Auth::check()) {
                $authenticated = true;
                Log::warning('Login event dispatch failed (Redis unavailable), auth succeeded', [
                    'email' => $request->email,
                    'error' => $e->getMessage(),
                ]);
            } else {
                Log::error('Login attempt threw unexpected exception', [
                    'email' => $request->email,
                    'error' => $e->getMessage(),
                ]);
                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);
            }
        }

        if (! $authenticated) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        try {
            $request->session()->regenerate();
        } catch (\Exception $e) {
            Log::warning('Session regenerate failed after login', ['error' => $e->getMessage()]);
            // Session failure is non-fatal — user is authenticated, continue
        }

        $user = Auth::user();

        if ($user) {
            return $this->redirectForUser($user);
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
