<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Support\Facades\Blade;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class StudaiPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->default()
            ->id('studai')
            ->path('studai')
            ->login()
            ->brandName('StudAI One')
            ->brandLogo(asset('images/logo.svg'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/favicon.png'))
            ->colors([
                'primary' => Color::hex('#2f5fb0'),
                'secondary' => Color::hex('#1c344d'),
                'success' => Color::hex('#1f8a5b'),
                'info' => Color::hex('#2f5fb0'),
                'warning' => Color::hex('#c9941a'),
                'danger' => Color::hex('#cf3a3a'),
            ])
            ->font('Inter')
            ->darkMode(false)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'User Management',
                'Content Management',
                'Business Operations',
                'Platform Health',
                'Settings',
            ])
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->maxContentWidth('full')
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => Blade::render(<<<'BLADE'
                    <style>
                        /* ── Mobile sidebar: narrow it so the close-overlay is large enough to tap ── */
                        @media (max-width: 1023px) {
                            .fi-sidebar {
                                width: min(260px, 75vw) !important;
                                max-width: min(260px, 75vw) !important;
                            }
                            /* Make the full overlay (including area beside sidebar) touchable */
                            .fi-sidebar-close-overlay {
                                cursor: pointer;
                                /* Visual hint: show a subtle "tap to close" affordance */
                            }
                            /* Prevent sidebar group buttons from stealing pointer events
                               outside the sidebar bounds */
                            .fi-sidebar-group-btn {
                                pointer-events: auto;
                            }
                        }
                    </style>
                BLADE)
            );

        // Enable database notifications only if table exists (prevents 500 when migrations are incomplete)
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                $panel->databaseNotifications()->databaseNotificationsPolling('30s');
            }
        } catch (\Throwable) {
            // notifications table unavailable — skip silently
        }

        return $panel;
    }
}
