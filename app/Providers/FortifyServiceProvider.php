<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Responses\LoginResponse;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\CanonicalizeUsername;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind custom login response that handles role-based redirects
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        // Custom login view
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // Custom registration view
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // Custom password reset views
        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forgot-password');
        });

        // Custom email verification view
        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        Fortify::resetPasswordView(function (Request $request) {
            return view('auth.reset-password', ['request' => $request]);
        });

        // Two factor authentication views
        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });

        Fortify::confirmPasswordView(function () {
            return view('auth.confirm-password');
        });

        // Custom authentication logic with account type check
        Fortify::authenticateUsing(function (Request $request) {
            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && (bool) $user->is_active && \Hash::check($request->password, $user->password)) {
                return $user;
            }
        });

        // Override the login pipeline to skip rate limiting.
        // The default Fortify rate limiter uses Redis cache which can be unavailable
        // on Azure, causing 500 errors on every login attempt. Rate limiting is
        // handled at the infrastructure level (Azure App Service / nginx).
        Fortify::loginThrough(function () {
            return array_filter([
                config('fortify.lowercase_usernames') ? CanonicalizeUsername::class : null,
                \Laravel\Fortify\Features::enabled(\Laravel\Fortify\Features::twoFactorAuthentication())
                    ? RedirectIfTwoFactorAuthenticatable::class
                    : null,
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
            ]);
        });
    }
}
