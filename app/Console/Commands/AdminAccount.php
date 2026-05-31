<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AdminAccount extends Command
{
    protected $signature = 'studai:admin-account
        {email : The administrator email address}
        {--name= : Display name (used only when creating a new account)}
        {--password= : Set an explicit password (otherwise a random one is generated)}
        {--super : Grant the super_admin role instead of admin}
        {--no-force-reset : Do not require a password change on first login}';

    protected $description = 'Create or reset a StudAI admin account (works on any environment/database).';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $role = $this->option('super') ? 'super_admin' : 'admin';
        $password = (string) ($this->option('password') ?: Str::password(14));
        $forceReset = ! $this->option('no-force-reset');

        $user = User::withTrashed()->where('email', $email)->first();
        $creating = $user === null;

        if ($creating) {
            $user = new User();
            $user->email = $email;
            $user->name = (string) ($this->option('name') ?: 'Administrator');
        }

        if ($user->trashed()) {
            $user->restore();
        }

        $user->forceFill([
            'password'             => Hash::make($password),
            'account_type'         => 'admin',
            'is_active'            => true,
            'force_password_reset' => $forceReset,
            'email_verified_at'    => $user->email_verified_at ?? now(),
        ])->save();

        // Ensure the spatie role exists and is assigned (guard: web).
        if (method_exists($user, 'syncRoles')) {
            Role::findOrCreate($role, 'web');
            $user->syncRoles([$role]);
        }

        $this->newLine();
        $this->info($creating ? 'Admin account created.' : 'Admin account updated.');
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', (string) $user->id],
                ['Email', $user->email],
                ['Role', $role],
                ['Active', 'yes'],
                ['Force reset on login', $forceReset ? 'yes' : 'no'],
                ['Password', $password],
            ]
        );
        $this->warn('Store this password securely. It is shown only once.');

        return self::SUCCESS;
    }
}
