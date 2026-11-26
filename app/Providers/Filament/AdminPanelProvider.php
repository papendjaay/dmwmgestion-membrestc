<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\CustomLogin;
use Filament\Pages\Dashboard;
use App\Filament\Pages\Auth\CustomRequestPasswordReset;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $plugins = [];
        if (class_exists(\BezhanSalleh\FilamentShield\FilamentShieldPlugin::class)) {
            $plugins[] = \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make();
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login(CustomLogin::class)
            ->passwordReset()
            ->profile()
            ->spa()
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->colors([
                'primary' => [
                    50 => '#eff8ff',
                    100 => '#dff0ff',
                    200 => '#b8e3ff',
                    300 => '#78ceff',
                    400 => '#30b5ff',
                    500 => '#009CFE',
                    600 => '#007cd9',
                    700 => '#0063b0',
                    800 => '#005491',
                    900 => '#064677',
                    950 => '#042c4f',
                ],
            ])
            ->brandName('D.M.W.M/COMITÉ TAMBA')
            ->favicon(asset('images/logo.png'))
            ->brandLogo(fn(): HtmlString => new HtmlString('
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="' . asset('images/logo.png') . '" alt="Logo D.M.W.M" style="height: 4rem; width: auto; object-fit: contain;" />
                    <span class="font-bold text-sm tracking-tight text-gray-900 dark:text-white whitespace-nowrap">D.M.W.M / COMITÉ TAMBA</span>
                </div>
            '))
            ->brandLogoHeight('3rem')
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn(): HtmlString => new HtmlString('
                    <style>
                        @keyframes pageFadeIn {
                            from { opacity: 0; transform: translateY(8px); }
                            to { opacity: 1; transform: translateY(0); }
                        }

                        h1, h2, h3, h4,
                        .fi-header-heading,
                        .fi-section-header-heading,
                        .fi-modal-heading,
                        .fi-wi-stats-overview-stat-value,
                        .fi-sidebar-header span {
                            font-family: "Poppins", sans-serif !important;
                            letter-spacing: -0.01em;
                        }

                        .fi-page-header-main-ctn {
                            padding-top: 0.65rem !important;
                            padding-bottom: 0.45rem !important;
                        }

                        .fi-wi {
                            gap: 0.85rem !important;
                        }

                        .fi-topbar nav {
                            border-bottom: 2px solid rgba(0, 156, 254, 0.2) !important;
                            backdrop-filter: blur(8px);
                        }

                        .fi-sidebar {
                            border-right: 1px solid rgba(0, 156, 254, 0.16) !important;
                        }

                        .fi-sidebar-item a {
                            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                            border-radius: 10px !important;
                        }

                        .fi-sidebar-item a:hover {
                            transform: translateX(4px);
                            background-color: rgba(243, 128, 20, 0.37) !important;
                        }

                        .fi-sidebar-item-active a {
                            background: linear-gradient(90deg, #009CFE 0%, #0284C7 100%) !important;
                            color: #ffffff !important;
                            font-weight: 700 !important;
                            box-shadow: 0 4px 12px -2px rgba(0, 156, 254, 0.4) !important;
                        }

                        .fi-sidebar-item-active a span,
                        .fi-sidebar-item-active a svg {
                            color: #ffffff !important;
                        }

                        .fi-wi-stats-overview-stat {
                            border-radius: 14px !important;
                            padding: 1.1rem 1.1rem !important;
                            border-top-width: 4px !important;
                            transition: transform 0.25s ease, box-shadow 0.25s ease !important;
                        }

                        .fi-wi-stats-overview-stat:hover {
                            transform: translateY(-4px);
                            box-shadow: 0 12px 24px -6px rgba(0, 156, 254, 0.22) !important;
                        }

                        .fi-wi-stats-overview-stat:nth-child(1) {
                            border-top-color: #006fb5 !important;
                            background: linear-gradient(145deg, #ffffff 55%, rgba(0, 156, 254, 0.1) 100%) !important;
                        }
                        .fi-wi-stats-overview-stat:nth-child(2) {
                            border-top-color: #00885a !important;
                            background: linear-gradient(145deg, #ffffff 55%, rgba(16, 185, 129, 0.1) 100%) !important;
                        }
                        .fi-wi-stats-overview-stat:nth-child(3) {
                            border-top-color: #ff6600 !important;
                            background: linear-gradient(145deg, #ffffff 55%, rgba(99, 102, 241, 0.1) 100%) !important;
                        }
                        .fi-wi-stats-overview-stat:nth-child(4) {
                            border-top-color: #f59e0b !important;
                            background: linear-gradient(145deg, #ffffff 55%, rgba(245, 158, 11, 0.1) 100%) !important;
                        }
                            /* Mode clair */
.fi-wi-stats-overview-stat:nth-child(1) { border-top-color: #006fb5 !important; }
.fi-wi-stats-overview-stat:nth-child(2) { border-top-color: #00885a !important; }
.fi-wi-stats-overview-stat:nth-child(3) { border-top-color: #ff6600 !important; }
.fi-wi-stats-overview-stat:nth-child(4) { border-top-color: #f59e0b !important; }

/* Adaptation automatique au mode sombre (.dark) */
.dark .fi-wi-stats-overview-stat:nth-child(1) {
    background: linear-gradient(145deg, rgba(15, 23, 42, 0.9) 55%, rgba(0, 156, 254, 0.18) 100%) !important;
}
.dark .fi-wi-stats-overview-stat:nth-child(2) {
    background: linear-gradient(145deg, rgba(15, 23, 42, 0.9) 55%, rgba(16, 185, 129, 0.18) 100%) !important;
}
.dark .fi-wi-stats-overview-stat:nth-child(3) {
    background: linear-gradient(145deg, rgba(15, 23, 42, 0.9) 55%, rgba(255, 102, 0, 0.18) 100%) !important;
}
.dark .fi-wi-stats-overview-stat:nth-child(4) {
    background: linear-gradient(145deg, rgba(15, 23, 42, 0.9) 55%, rgba(245, 158, 11, 0.18) 100%) !important;
}

                    </style>
                ')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->plugins($plugins)
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
