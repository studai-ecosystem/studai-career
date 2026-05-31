<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Traits\RedirectsAuthenticatedUsers;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    use RedirectsAuthenticatedUsers;

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

        return $this->redirectForUser($user);
    }
}
