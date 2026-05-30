<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     * Redirects authenticated users away from guest-only routes.
     * Admins go to the Filament panel; others go to /dashboard.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
                    return redirect('/studai');
                }

                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
