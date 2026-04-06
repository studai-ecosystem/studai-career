<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        if (app()->bound('sentry')) {
            \Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
                $scope->addEventProcessor(function (\Sentry\Event $event, ?\Sentry\EventHint $hint): ?\Sentry\Event {
                    // Add correlation ID to all events
                    if (class_exists(\App\Http\Middleware\CorrelationIdMiddleware::class)) {
                        $correlationId = \App\Http\Middleware\CorrelationIdMiddleware::getCorrelationId();
                        if ($correlationId) {
                            $event->setTag('correlation_id', $correlationId);
                        }
                    }

                    // Add user context if authenticated
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
