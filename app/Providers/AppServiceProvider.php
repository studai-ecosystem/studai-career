<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Force HTTPS on Azure App Service (HTTPS terminated at load balancer) ──────
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        // ── Strict mode in local/testing: catches N+1, lazy loading, mass assignment ──
        Model::shouldBeStrict(! app()->isProduction());

        // ── Enforce strong passwords globally ─────────────────────────────────────────
        Password::defaults(fn () => app()->isProduction()
            ? Password::min(8)->letters()->mixedCase()->numbers()->uncompromised()
            : Password::min(8)
        );

        // ── Sentry user context ───────────────────────────────────────────────────────
        if (app()->bound('sentry')) {
            \Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
                $scope->addEventProcessor(function (\Sentry\Event $event, ?\Sentry\EventHint $hint): ?\Sentry\Event {
                    if (class_exists(\App\Http\Middleware\CorrelationIdMiddleware::class)) {
                        $correlationId = \App\Http\Middleware\CorrelationIdMiddleware::getCorrelationId();
                        if ($correlationId) {
                            $event->setTag('correlation_id', $correlationId);
                        }
                    }

                    if (auth()->check()) {
                        $user = auth()->user();
                        if ($user) {
                            \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($user): void {
                                $scope->setUser([
                                    'id' => (string) $user->id,
                                    'email' => $user->email,
                                    'username' => $user->name,
                                ]);
                            });
                        }
                    }

                    return $event;
                });
            });
        }
    }
}
