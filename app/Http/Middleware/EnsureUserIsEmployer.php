<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsEmployer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to continue.');
        }

        $user = auth()->user();

        // Non-employer trying to access employer routes — redirect gracefully
        if (!$user->isEmployer()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Employer account required.'], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'This area is for employer accounts only. Please log in with a company account.');
        }

        // Employer has no company yet — send them to onboarding (not a hard 403)
        if (!$user->company && !$request->routeIs('employer.onboarding') && !$request->routeIs('employer.onboarding.save')) {
            return redirect()->route('employer.onboarding')
                ->with('info', 'Please complete your company setup first.');
        }

        return $next($request);
    }
}
