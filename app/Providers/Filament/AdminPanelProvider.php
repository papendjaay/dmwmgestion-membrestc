<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\CustomLogin;
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
            ->path('admin')
            ->login(CustomLogin::class)
            ->spa()
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

            // 1. Titre officiel et Favicon
            ->brandName('D.M.W.M/COMITÉ TAMBA')
            ->favicon(asset('images/logo.png'))

            // 2. Logo + Titre dans la barre latérale
            ->brandLogo(fn(): HtmlString => new HtmlString('
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="' . asset('images/logo.png') . '" alt="Logo D.M.W.M" style="height: 5rem; width: auto; object-fit: contain;" />
                    <span class="font-bold text-sm tracking-tight text-gray-900 dark:text-white whitespace-nowrap">D.M.W.M / COMITÉ TAMBA</span>
                </div>
            '))
            ->brandLogoHeight('3rem')

            // 3. Thème global coloré, compact, pleine largeur et animé
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

                        /* Réduction des espacements verticaux pour afficher plus d\'éléments sans défiler */
                        .fi-page-header-main-ctn {
                            padding-top: 0.65rem !important;
                            padding-bottom: 0.45rem !important;
                        }

                        .fi-wi {
                            gap: 0.85rem !important;
                        }

                        /* Barre supérieure (Topbar) soulignée */
                        .fi-topbar nav {
                            border-bottom: 2px solid rgba(0, 156, 254, 0.2) !important;
                            backdrop-filter: blur(8px);
                        }

                        /* Animation et coloration du menu latéral (Sidebar) */
                        .fi-sidebar {
                            border-right: 1px solid rgba(0, 156, 254, 0.16) !important;
                        }

                        .fi-sidebar-item a {
                            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
                            border-radius: 10px !important;
                        }

                        .fi-sidebar-item a:hover {
                            transform: translateX(4px);
                            background-color: rgba(0, 156, 254, 0.12) !important;
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

                        /* Coloration individuelle et élévation des 4 cartes statistiques */
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
                            border-top-color: #fad900 !important;
                            background: linear-gradient(145deg, #ffffff 55%, rgba(245, 158, 11, 0.1) 100%) !important;
                        }

                        .dark .fi-wi-stats-overview-stat:nth-child(1),
                        .dark .fi-wi-stats-overview-stat:nth-child(2),
                        .dark .fi-wi-stats-overview-stat:nth-child(3),
                        .dark .fi-wi-stats-overview-stat:nth-child(4) {
                            background: #162032 !important;
                        }

                        /* Sections et Graphiques */
                        .fi-section {
                            border-radius: 14px !important;
                            box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.06) !important;
                            transition: transform 0.30s ease, box-shadow 0.30s ease !important;
                        }

                        .fi-section:hover {
                            box-shadow: 0 10px 25px -5px rgba(0, 156, 254, 0.15) !important;
                        }

                        /* Boutons interactifs */
                        .fi-btn {
                            border-radius: 10px !important;
                            transition: transform 0.5s ease, box-shadow 0.5s ease !important;
                        }

                        .fi-btn:hover {
                            transform: translateY(-1.5px);
                            box-shadow: 0 6px 14px -3px rgba(0, 156, 254, 0.28) !important;
                        }

                        /* Champs de formulaire */
                        .fi-input-wrp {
                            border-radius: 10px !important;
                            transition: box-shadow 0.25s ease, border-color 0.25s ease !important;
                        }

                        .fi-input-wrp:focus-within {
                            box-shadow: 0 0 0 3px rgba(0, 156, 254, 0.22) !important;
                        }

                        /* Modales avec ouverture douce */
                        .fi-modal-window {
                            border-radius: 16px !important;
                            animation: pageFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                        }
                            .fi-wi-chart .fi-section-content {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

.fi-wi-chart canvas {
    max-height: 215px !important;
}
    .welcome-banner-card {
    padding: 12px 18px !important;
}
    .fi-wi-table thead {
    background-color: #009CFE !important;
}

.fi-wi-table thead th,
.fi-wi-table thead th * {
    color: #000000 !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    font-size: 0.75rem !important;
}

.fi-wi-table tbody tr:nth-child(even) {
    background-color: #F3F3F3 !important;
}

.dark .fi-wi-table tbody tr:nth-child(even) {
    background-color: #1E293B !important;
}

.fi-wi-table tbody tr:hover {
    background-color: #E0E0E0 !important;
}

.dark .fi-wi-table tbody tr:hover {
    background-color: #334155 !important;
}

.fi-wi-table tbody td {
    font-weight: 600 !important;
    font-size: 0.75rem !important;
    text-transform: uppercase !important;
    padding-top: 0.45rem !important;
    padding-bottom: 0.45rem !important;
}
    /* Compactage du tableau sur le Dashboard */
.fi-wi-table .fi-ta-header-ctn {
    padding: 0.45rem 1rem !important;
}

.fi-wi-table .fi-ta-cell,
.fi-wi-table .fi-ta-header-cell {
    padding-top: 0.35rem !important;
    padding-bottom: 0.35rem !important;
}
    /* Alignement parfait des 3 graphiques sur la même ligne */
.fi-wi-chart {
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
}

.fi-wi-chart .fi-section {
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
}


.fi-wi-chart .fi-section-header {
    min-height: 4.5rem !important;
    align-items: flex-start !important;
}

/* Uniformise la hauteur de la zone du canvas Chart.js */
.fi-wi-chart .fi-section-content-ctn,
.fi-wi-chart .fi-section-content {
    flex: 1 1 auto !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
}

.fi-wi-chart canvas {
    height: 320px !important;
    max-height: 320px !important;
}


                    </style>
                '),
            )

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([
                // Vide pour éviter le doublon avec App\Filament\Pages\Dashboard
            ])
            ->sidebarWidth('16rem')
            ->plugins($plugins)
            ->maxContentWidth('full')
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
            ->sidebarCollapsibleOnDesktop()
            ->globalSearch(false)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
