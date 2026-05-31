<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\RedirectsAuthenticatedUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ForcePasswordController extends Controller
{
    use RedirectsAuthenticatedUsers;

    /**
     * Show the forced password-change screen.
     */
    public function edit(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user === null || $user->force_password_reset !== true) {
            return redirect()->route('dashboard');
        }

        return view('auth.force-password');
    }

    /**
     * Persist the new password and clear the forced-reset flag.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        if (Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors([
                'password' => 'Please choose a password different from your temporary one.',
            ]);
        }

        $user->forceFill([
            'password'             => Hash::make($request->input('password')),
            'force_password_reset' => false,
            'password_changed_at'  => now(),
        ])->save();

        $request->session()->regenerate();

        return $this->redirectForUser($user)->with('success', 'Your password has been updated.');
    }
}
