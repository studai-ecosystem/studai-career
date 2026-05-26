<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialProviderSeeder extends Seeder
{
    /**
     * Seed the social_providers table with Google, GitHub, and LinkedIn.
     *
     * Credentials are read from environment variables so this seeder works
     * in any environment without hardcoded secrets.
     */
    public function run(): void
    {
        $providers = [
            [
                'name'              => 'Google',
                'slug'              => 'google',
                'client_id'         => env('GOOGLE_CLIENT_ID', ''),
                'redirect_url'      => env('GOOGLE_REDIRECT_URI', url('/auth/google/callback')),
                'scopes'            => json_encode(['openid', 'profile', 'email']),
                'additional_config' => json_encode([]),
                'icon'              => 'google',
                'color'             => '#4285F4',
                'is_enabled'        => !empty(env('GOOGLE_CLIENT_ID')),
                'allow_login'       => true,
                'allow_register'    => true,
                'sort_order'        => 1,
            ],
            [
                'name'              => 'GitHub',
                'slug'              => 'github',
                'client_id'         => env('GITHUB_CLIENT_ID', ''),
                'redirect_url'      => env('GITHUB_REDIRECT_URI', url('/auth/github/callback')),
                'scopes'            => json_encode(['user:email', 'read:user']),
                'additional_config' => json_encode([]),
                'icon'              => 'github',
                'color'             => '#24292E',
                'is_enabled'        => !empty(env('GITHUB_CLIENT_ID')),
                'allow_login'       => true,
                'allow_register'    => true,
                'sort_order'        => 2,
            ],
            [
                'name'              => 'LinkedIn',
                'slug'              => 'linkedin-openid',
                'client_id'         => env('LINKEDIN_CLIENT_ID', ''),
                'redirect_url'      => env('LINKEDIN_REDIRECT_URI', url('/auth/linkedin-openid/callback')),
                'scopes'            => json_encode(['openid', 'profile', 'email']),
                'additional_config' => json_encode([]),
                'icon'              => 'linkedin',
                'color'             => '#0A66C2',
                'is_enabled'        => !empty(env('LINKEDIN_CLIENT_ID')),
                'allow_login'       => true,
                'allow_register'    => true,
                'sort_order'        => 3,
            ],
            [
                'name'              => 'Facebook',
                'slug'              => 'facebook',
                'client_id'         => env('FACEBOOK_CLIENT_ID', ''),
                'redirect_url'      => env('FACEBOOK_REDIRECT_URI', url('/auth/facebook/callback')),
                'scopes'            => json_encode(['email', 'public_profile']),
                'additional_config' => json_encode([]),
                'icon'              => 'facebook',
                'color'             => '#1877F2',
                'is_enabled'        => !empty(env('FACEBOOK_CLIENT_ID')),
                'allow_login'       => true,
                'allow_register'    => true,
                'sort_order'        => 4,
            ],
        ];

        foreach ($providers as $provider) {
            $clientId     = $provider['client_id'];
            $clientSecret = null;

            // Encrypt the client secret from env if available
            $envKey = strtoupper(str_replace('-', '_', $provider['slug'])) . '_CLIENT_SECRET';
            $rawSecret = env($envKey, '');
            if (!empty($rawSecret)) {
                $clientSecret = \Illuminate\Support\Facades\Crypt::encryptString($rawSecret);
            }

            DB::table('social_providers')->updateOrInsert(
                ['slug' => $provider['slug']],
                [
                    'name'              => $provider['name'],
                    'slug'              => $provider['slug'],
                    'client_id'         => $clientId,
                    'client_secret'     => $clientSecret,
                    'redirect_url'      => $provider['redirect_url'],
                    'scopes'            => $provider['scopes'],
                    'additional_config' => $provider['additional_config'],
                    'icon'              => $provider['icon'],
                    'color'             => $provider['color'],
                    'is_enabled'        => (int) $provider['is_enabled'],
                    'allow_login'       => (int) $provider['allow_login'],
                    'allow_register'    => (int) $provider['allow_register'],
                    'sort_order'        => $provider['sort_order'],
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]
            );
        }

        $this->command->info('Social providers seeded (Google, GitHub, LinkedIn, Facebook).');
        $this->command->warn('Add GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GITHUB_CLIENT_ID, GITHUB_CLIENT_SECRET, LINKEDIN_CLIENT_ID, LINKEDIN_CLIENT_SECRET to .env to enable social login.');
    }
}
