<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response after successful login.
     */
    public function toResponse($request): \Symfony\Component\HttpFoundation\Response
    {
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            if (\Route::has('filament.studai.pages.dashboard')) {
                return redirect()->intended(route('filament.studai.pages.dashboard'));
            }
            return redirect()->intended(route('dashboard'));
        }

        if ($user->isEmployer()) {
            return redirect()->intended(route('employer.home'));
        }

        return redirect()->intended(route('dashboard'));
    }
}
