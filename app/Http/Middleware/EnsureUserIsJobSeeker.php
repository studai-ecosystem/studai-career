<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsJobSeeker
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->account_type === 'employer') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This feature is for job seekers only.'], 403);
            }
            return redirect()->route('employer.dashboard')
                ->with('error', 'The AI Job Agent is for job seekers only.');
        }

        return $next($request);
    }
}
