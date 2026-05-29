<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Mail\LoginAlertMail;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends a security "new sign-in" email whenever a user logs in.
 *
 * Runs synchronously (not queued) so the current request's IP and
 * user-agent are available. Mail failures are caught so they never
 * block the login flow.
 */
class SendLoginNotification
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! $user instanceof User || empty($user->email)) {
            return;
        }

        try {
            $request   = request();
            $ipAddress = $request?->ip() ?? 'Unknown';
            $device    = $request?->userAgent() ?? 'Unknown device';
            $loginTime = now()->format('d M Y, h:i A') . ' UTC';

            Mail::to($user->email)->send(new LoginAlertMail($user, $ipAddress, $device, $loginTime));
        } catch (\Throwable $e) {
            Log::warning('Login notification email failed', [
                'user_id' => $user->id ?? null,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
