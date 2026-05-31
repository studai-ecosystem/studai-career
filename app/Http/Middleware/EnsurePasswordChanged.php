<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Routes that an authenticated user with a pending forced password reset
     * is still allowed to reach (so they can actually change it or log out).
     *
     * @var list<string>
     */
    private array $allowedRoutes = [
        'password.force',
        'password.force.update',
        'logout',
    ];

    /**
     * Redirect authenticated users flagged for a forced password reset to the
     * dedicated change-password page before they can use the rest of the app.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user === null || $user->force_password_reset !== true) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        if ($routeName !== null && in_array($routeName, $this->allowedRoutes, true)) {
            return $next($request);
        }

        // Never trap non-page (asset/api) calls or the Filament livewire updates.
        if ($request->expectsJson() || $request->ajax()) {
            return $next($request);
        }

        return redirect()->route('password.force');
    }
}
