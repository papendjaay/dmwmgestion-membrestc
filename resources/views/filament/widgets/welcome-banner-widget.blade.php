<x-filament-widgets::widget>
    <style>
        @keyframes fadeSlideUp {
            0% {
                opacity: 0;
                transform: translateY(12px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulseRing {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .welcome-banner-card {
            animation: fadeSlideUp 0.55s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            background: linear-gradient(135deg, rgba(0, 156, 254, 0.08) 0%, rgba(255, 255, 255, 0.95) 55%, rgba(0, 156, 254, 0.04) 100%);
            border: 1px solid rgba(0, 156, 254, 0.22);
            border-left: 5px solid #009CFE;
            border-radius: 14px;
            padding: 20px 24px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .dark .welcome-banner-card {
            background: linear-gradient(135deg, rgba(0, 156, 254, 0.14) 0%, rgba(30, 41, 59, 0.95) 60%, rgba(15, 23, 42, 0.98) 100%);
            border-color: rgba(0, 156, 254, 0.3);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.3);
        }

        .welcome-banner-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -4px rgba(0, 156, 254, 0.15);
        }

        .welcome-banner-flex {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .welcome-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .avatar-wrapper {
            position: relative;
            display: inline-flex;
        }

        .online-indicator {
            position: absolute;
            bottom: 1px;
            right: 1px;
            width: 12px;
            height: 12px;
            background-color: #10B981;
            border: 2px solid #ffffff;
            border-radius: 9999px;
            animation: pulseRing 2s infinite;
        }

        .dark .online-indicator {
            border-color: #1E293B;
        }

        .role-pill {
            display: inline-flex;
            align-items: center;
            padding: 2px 10px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #0284C7;
            background-color: rgba(0, 156, 254, 0.12);
            border: 1px solid rgba(0, 156, 254, 0.25);
            border-radius: 9999px;
        }

        .dark .role-pill {
            color: #38BDF8;
            background-color: rgba(56, 189, 248, 0.15);
        }

        .welcome-right {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }

        .date-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 14px;
            font-size: 0.82rem;
            font-weight: 600;
            text-transform: capitalize;
            color: #334155;
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
        }

        .dark .date-pill {
            color: #E2E8F0;
            background-color: #0F172A;
            border-color: #334155;
        }
    </style>

    <div class="welcome-banner-card">
        <div class="welcome-banner-flex">
            {{-- Partie gauche : Avatar animé + Identité + Rôle --}}
            <div class="welcome-left">
                <div class="avatar-wrapper">
                    <x-filament-panels::avatar.user size="lg" :user="$user" />
                    <span class="online-indicator" title="Connecté"></span>
                </div>

                <div>
                    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
                        <h2 style="margin: 0; font-size: 1.15rem; font-weight: 800;" class="text-gray-950 dark:text-white">
                            Bienvenue, {{ $user->name }}
                        </h2>
                        <span class="role-pill">
                            {{ $role }}
                        </span>
                    </div>

                    <p style="margin: 4px 0 0 0; font-size: 0.85rem;" class="text-gray-600 dark:text-gray-300">
                        Plateforme de gestion — <strong style="color: #009CFE; font-weight: 700;">D.M.W.M / COMITÉ TAMBA</strong>
                    </p>
                </div>
            </div>

            {{-- Partie droite : Badge Date + Bouton Déconnexion --}}
            <div class="welcome-right">
                <div class="date-pill">
                    <x-filament::icon
                        icon="heroicon-o-calendar-days"
                        style="width: 18px; height: 18px; color: #009CFE;" />
                    <span>{{ now()->translatedFormat('l d F Y') }}</span>
                </div>

                <form action="{{ filament()->getLogoutUrl() }}" method="post" style="margin: 0;">
                    @csrf
                    <x-filament::button
                        color="danger"
                        outlined
                        icon="heroicon-m-arrow-right-on-rectangle"
                        type="submit"
                        size="sm">
                        Déconnexion
                    </x-filament::button>
                </form>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>