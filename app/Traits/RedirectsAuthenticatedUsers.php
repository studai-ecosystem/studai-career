<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

trait RedirectsAuthenticatedUsers
{
    /**
     * Resolve the correct landing destination for a freshly authenticated user.
     * Users flagged for a forced password reset are always sent to the
     * change-password page first, before any role-based dashboard.
     */
    protected function redirectForUser(User $user): RedirectResponse
    {
        if ($user->force_password_reset === true) {
            return redirect()->route('password.force');
        }

        if ($user->isAdmin()) {
            if (Route::has('filament.studai.pages.dashboard')) {
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
