<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Support\Facades\Blade;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('WMS Gudang Pusat')
            ->brandLogo(fn () => view('filament.components.brand-logo'))
            ->brandLogoHeight('auto')
            ->favicon(asset('images/logo-hsk.png'))
            ->maxContentWidth(\Filament\Support\Enums\MaxWidth::Full)
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->font('Montserrat')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render(<<<'HTML'
                    <link rel="preconnect" href="https://fonts.googleapis.com">
                    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
                    <script src="https://cdn.tailwindcss.com"></script>
                    <script>
                        tailwind.config = {
                            darkMode: 'class',
                            corePlugins: {
                                preflight: false,
                            },
                            theme: {
                                extend: {
                                    fontFamily: {
                                        sans: ['Montserrat', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                                    },
                                },
                            },
                        }
                    </script>
                    <style>
                        /* Set Font Montserrat Secara Global ke Seluruh Elemen Tampilan */
                        html, body, .fi-body, .fi-main, .fi-sidebar, .fi-topbar, .fi-modal, input, select, textarea, button, table, .dt-container {
                            font-family: 'Montserrat', sans-serif !important;
                            -webkit-font-smoothing: antialiased;
                        }
                        .choices__list--dropdown .choices__item,
                        .choices__list[aria-expanded] .choices__item {
                            padding: 0.625rem 0.875rem !important;
                            font-size: 0.875rem !important;
                            line-height: 1.35rem !important;
                            font-weight: 500 !important;
                            font-family: 'Montserrat', sans-serif !important;
                        }
                        .choices__input {
                            font-size: 0.875rem !important;
                            font-family: 'Montserrat', sans-serif !important;
                        }
                    </style>
                HTML)
            )
            ->navigationGroups([
                'Gudang & Inventori',
                'Transaksi & Dokumen',
                'Audit & Penyesuaian',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets bawaan (AccountWidget & FilamentInfoWidget) disembunyikan
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
            ]);
    }
}
