<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            \Log::info('=== LOGIN ATTEMPT START ===', [
                'email' => $request->email,
                'timestamp' => now(),
            ]);
            
            $request->authenticate();
            \Log::info('✓ Authentication successful');

            $request->session()->regenerate();
            \Log::info('✓ Session regenerated');

            $dashboardRoute = route('dashboard', absolute: false);
            \Log::info('✓ Dashboard route resolved', ['route' => $dashboardRoute]);
            
            return redirect()->intended($dashboardRoute);
        } catch (\Exception $e) {
            \Log::error('✗ LOGIN FAILED', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
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
