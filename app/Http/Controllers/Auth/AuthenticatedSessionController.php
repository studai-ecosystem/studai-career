<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
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
            // Check if this is a connection/queue error (not an auth error).
            // Auth itself may have succeeded; only the event dispatch failed.
            if (Auth::check()) {
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

        // Role-based redirect
        if ($user && $user->isAdmin()) {
            if (\Illuminate\Support\Facades\Route::has('filament.studai.pages.dashboard')) {
                return redirect()->intended(route('filament.studai.pages.dashboard'));
            }
            return redirect()->intended(route('dashboard'));
        }

        if ($user && $user->isEmployer()) {
            return redirect()->intended(route('employer.home'));
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
