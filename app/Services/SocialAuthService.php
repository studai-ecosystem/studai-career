<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SocialAccount;
use App\Models\SocialAuthLog;
use App\Models\SocialProvider;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Two\AbstractProvider;

class SocialAuthService
{
    /**
     * Get all enabled providers for display.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEnabledProviders()
    {
        return SocialProvider::enabled()
            ->configured()
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get provider configuration from database.
     */
    public function getProvider(string $slug): ?SocialProvider
    {
        try {
            return SocialProvider::where('slug', $slug)
                ->enabled()
                ->configured()
                ->first();
        } catch (\Throwable $e) {
            // social_providers table may not exist on every environment; the
            // caller can still fall back to .env credentials.
            return null;
        }
    }

    /**
     * Configure Socialite with database credentials.
     */
    public function configureProvider(string $slug): ?AbstractProvider
    {
        $provider = $this->getProvider($slug);

        if ($provider) {
            $config = $provider->getSocialiteConfig();

            // Override Laravel config at runtime so Socialite picks up DB credentials
            config(["services.{$slug}" => $config]);

            $driver = Socialite::driver($slug);

            // Apply scopes if specified
            $scopes = $provider->getScopesArray();
            if (!empty($scopes)) {
                $driver->scopes($scopes);
            }

            return $driver;
        }

        // Fallback: use credentials from config/services.php (.env) when no DB
        // row exists. This lets "Continue with Google" work out of the box once
        // GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET are set, without seeding a row.
        if ($this->hasEnvCredentials($slug)) {
            $envConfig = (array) config("services.{$slug}");

            if (empty($envConfig['redirect'])) {
                $envConfig['redirect'] = route('social.callback', ['provider' => $slug]);
                config(["services.{$slug}" => $envConfig]);
            }

            return Socialite::driver($slug);
        }

        return null;
    }

    /**
     * Whether a provider has usable credentials in config/services.php (.env).
     */
    public function hasEnvCredentials(string $slug): bool
    {
        $config = config("services.{$slug}");

        return is_array($config)
            && !empty($config['client_id'])
            && !empty($config['client_secret']);
    }

    /**
     * Whether a provider can be used for auth, via DB row or .env credentials.
     */
    public function isProviderAvailable(string $slug): bool
    {
        // Cheap env check first so it works even without the social_providers table.
        if ($this->hasEnvCredentials($slug)) {
            return true;
        }

        return $this->getProvider($slug) !== null;
    }

    /**
     * Redirect to OAuth provider.
     */
    public function redirect(string $provider)
    {
        $driver = $this->configureProvider($provider);

        if (!$driver) {
            throw new \Exception("Provider '{$provider}' is not configured or enabled.");
        }

        // Log the redirect attempt
        SocialAuthLog::logSuccess($provider, 'redirect');

        return $driver->redirect();
    }

    /**
     * Handle OAuth callback.
     */
    public function handleCallback(string $provider): array
    {
        $driver = $this->configureProvider($provider);

        if (!$driver) {
            throw new \Exception("Provider '{$provider}' is not configured or enabled.");
        }

        try {
            $socialUser = $driver->user();

            return $this->findOrCreateUser($provider, $socialUser);
        } catch (\Exception $e) {
            Log::error('Social auth callback failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            SocialAuthLog::logFailure(
                $provider,
                'callback',
                $e->getMessage()
            );

            throw $e;
        }
    }

    /**
     * Find or create user from social login.
     */
    protected function findOrCreateUser(string $provider, SocialiteUser $socialUser): array
    {
        return DB::transaction(function () use ($provider, $socialUser) {
            // Check if we already have this social account linked
            $socialAccount = SocialAccount::forProviderUser($provider, $socialUser->getId())->first();

            if ($socialAccount) {
                // Existing social account - update and log in
                $user = $socialAccount->user;
                $isNewUser = false;

                // Update tokens and profile
                $this->updateSocialAccount($socialAccount, $socialUser);

                SocialAuthLog::logSuccess($provider, 'login', $user->id, [
                    'email' => $socialUser->getEmail(),
                ]);
            } else {
                // Check if user exists with same email
                $email = $socialUser->getEmail();
                $user = $email ? User::where('email', $email)->first() : null;

                if ($user) {
                    // Link to existing account
                    $socialAccount = $this->createSocialAccount($user, $provider, $socialUser);
                    $isNewUser = false;

                    SocialAuthLog::logSuccess($provider, 'link', $user->id, [
                        'email' => $email,
                    ]);
                } else {
                    // Create new user
                    $user = $this->createUser($socialUser);
                    $socialAccount = $this->createSocialAccount($user, $provider, $socialUser);
                    $isNewUser = true;

                    SocialAuthLog::logSuccess($provider, 'register', $user->id, [
                        'email' => $socialUser->getEmail(),
                    ]);
                }
            }

            return [
                'user' => $user,
                'social_account' => $socialAccount,
                'is_new_user' => $isNewUser,
            ];
        });
    }

    /**
     * Create a new user from social data.
     */
    protected function createUser(SocialiteUser $socialUser): User
    {
        $name = $socialUser->getName() ?? $socialUser->getNickname() ?? 'User';
        $email = $socialUser->getEmail();

        // Generate unique email if none provided
        if (!$email) {
            $email = Str::slug($name) . '-' . Str::random(8) . '@social.local';
        }

        // Respect the account type chosen on the "Continue with Google" button
        // (student vs company). Defaults to job_seeker for safety.
        $accountType = session('social_auth_role') === 'employer' ? 'employer' : 'job_seeker';

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make(Str::random(32)),
            'email_verified_at' => now(), // Social login = verified
            'account_type' => $accountType,
            'is_active' => true,
        ]);

        // Mirror standard registration: assign role + seed job seeker profile.
        try {
            $user->assignRole($accountType);
        } catch (\Throwable $e) {
            Log::warning('Social signup role assignment failed', [
                'account_type' => $accountType,
                'error' => $e->getMessage(),
            ]);
        }

        if ($accountType === 'job_seeker') {
            try {
                $user->profile()->create(['profile_completeness' => 10]);
            } catch (\Throwable $e) {
                // Profile may already exist or schema differs; non-fatal.
            }
        }

        return $user;
    }

    /**
     * Create a social account link.
     */
    protected function createSocialAccount(User $user, string $provider, SocialiteUser $socialUser): SocialAccount
    {
        $expiresIn = null;
        $refreshToken = null;

        // Handle different Socialite user types
        if (method_exists($socialUser, 'expiresIn')) {
            $expiresIn = $socialUser->expiresIn;
        }
        if (method_exists($socialUser, 'refreshToken')) {
            $refreshToken = $socialUser->refreshToken;
        }

        return SocialAccount::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_user_id' => $socialUser->getId(),
            'email' => $socialUser->getEmail(),
            'name' => $socialUser->getName(),
            'nickname' => $socialUser->getNickname(),
            'avatar' => $socialUser->getAvatar(),
            'access_token' => $socialUser->token,
            'refresh_token' => $refreshToken,
            'token_expires_at' => $expiresIn ? now()->addSeconds($expiresIn) : null,
            'profile_data' => $socialUser->getRaw(),
            'last_login_at' => now(),
        ]);
    }

    /**
     * Update existing social account.
     */
    protected function updateSocialAccount(SocialAccount $account, SocialiteUser $socialUser): void
    {
        $expiresIn = null;
        $refreshToken = $account->refresh_token;

        if (method_exists($socialUser, 'expiresIn')) {
            $expiresIn = $socialUser->expiresIn;
        }
        if (method_exists($socialUser, 'refreshToken') && $socialUser->refreshToken) {
            $refreshToken = $socialUser->refreshToken;
        }

        $account->update([
            'email' => $socialUser->getEmail() ?? $account->email,
            'name' => $socialUser->getName() ?? $account->name,
            'nickname' => $socialUser->getNickname() ?? $account->nickname,
            'avatar' => $socialUser->getAvatar() ?? $account->avatar,
            'access_token' => $socialUser->token,
            'refresh_token' => $refreshToken,
            'token_expires_at' => $expiresIn ? now()->addSeconds($expiresIn) : $account->token_expires_at,
            'profile_data' => $socialUser->getRaw(),
            'last_login_at' => now(),
        ]);
    }

    /**
     * Disconnect a social account from user.
     */
    public function disconnect(User $user, string $provider): bool
    {
        // Check if user has other login methods
        $socialAccountsCount = $user->socialAccounts()->count();
        $hasPassword = $user->password !== null && $user->password !== '';

        if ($socialAccountsCount <= 1 && !$hasPassword) {
            throw new \Exception('Cannot disconnect the only login method. Please add a password first.');
        }

        $account = $user->socialAccounts()->forProvider($provider)->first();

        if (!$account) {
            return false;
        }

        SocialAuthLog::logSuccess($provider, 'disconnect', $user->id);

        return (bool) $account->delete();
    }

    /**
     * Get user's connected social accounts.
     */
    public function getConnectedAccounts(User $user)
    {
        return $user->socialAccounts()
            ->get()
            ->keyBy('provider');
    }

    /**
     * Get available providers for a user to connect.
     */
    public function getAvailableProviders(User $user)
    {
        $connected = $this->getConnectedAccounts($user)->keys()->toArray();
        
        return $this->getEnabledProviders()
            ->map(function ($provider) use ($connected) {
                $provider->is_connected = in_array($provider->slug, $connected);
                return $provider;
            });
    }

    /**
     * Get provider-specific scopes.
     */
    public function getProviderScopes(string $provider): array
    {
        $defaults = [
            'google' => ['openid', 'email', 'profile'],
            'linkedin' => ['r_liteprofile', 'r_emailaddress'],
            'apple' => ['name', 'email'],
            'microsoft' => ['User.Read', 'email', 'profile', 'openid'],
            'facebook' => ['email', 'public_profile'],
            'github' => ['user:email', 'read:user'],
            'twitter' => [],
        ];

        return $defaults[$provider] ?? [];
    }
}
