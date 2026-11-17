<?php

namespace App\Filament\Widgets;

use App\Models\Conseil;
use App\Models\Enrolement;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrolementsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Évolution des Enrôlements';
    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * Sélecteur de vue en haut à droite du widget selon le rôle
     */
    protected function getFilters(): ?array
    {
        $user = Auth::user();
        $isAdmin = $user instanceof User && $user->hasRole('super_admin');

        $currentYear = now()->year;
        $lastYear = $currentYear - 1;

        $filters = [
            "genre_$currentYear"  => "Par Genre ($currentYear)",
            "cumul_$currentYear"  => "Cumul progressif ($currentYear)",
            'compare_years'       => "Comparaison $currentYear vs $lastYear",
        ];

        // Seul le super_admin peut comparer les conseils entre eux
        if ($isAdmin) {
            $filters["conseil_$currentYear"] = "Par Conseil ($currentYear)";
        }

        return $filters;
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $isAdmin = $user instanceof User && $user->hasRole('super_admin');
        $maSection = ! $isAdmin && $user instanceof User ? $user->getMaSection() : null;

        $conseil = $this->pageFilters['conseil'] ?? null;
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        $currentYear = now()->year;
        $activeFilter = $this->filter ?? "genre_$currentYear";

        // Base de requête sécurisée et filtrée
        $baseQuery = Enrolement::query()
            ->when(! $isAdmin, fn($q) => $maSection ? $q->where('Section', $maSection->Nom) : $q->whereRaw('1 = 0'))
            ->when($isAdmin && $conseil, fn($q) => $q->where('Conseil', $conseil))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));

        $labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];

        // =========================================================================
        // MODE 1 : Comparaison par Conseil (super_admin uniquement)
        // =========================================================================
        if ($isAdmin && str_starts_with($activeFilter, 'conseil_')) {
            $year = (int) str_replace('conseil_', '', $activeFilter);

            $conseils = Conseil::whereNotNull('Nom')->pluck('Nom')->unique()->values()->toArray();

            $rows = (clone $baseQuery)
                ->selectRaw('MONTH(created_at) as month, Conseil, COUNT(*) as total')
                ->whereYear('created_at', $year)
                ->whereNotNull('Conseil')
                ->groupByRaw('MONTH(created_at), Conseil')
                ->get();

            // Ajoute d'éventuels conseils présents dans Enrolement mais absents de la table Conseil
            $conseilsEnrolements = $rows->pluck('Conseil')->unique()->toArray();
            $tousConseils = array_values(array_unique(array_merge($conseils, $conseilsEnrolements)));

            // Palette de couleurs distinctes pour chaque conseil
            $palette = [
                '#3b82f6', // Bleu
                '#10b981', // Vert
                '#f59e0b', // Orange
                '#ef4444', // Rouge
                '#8b5cf6', // Violet
                '#ec4899', // Rose
                '#06b6d4', // Cyan
                '#84cc16', // Vert lime
            ];

            $datasets = [];
            foreach ($tousConseils as $index => $nomConseil) {
                $monthlyData = array_fill(0, 12, 0);

                foreach ($rows->where('Conseil', $nomConseil) as $row) {
                    $monthlyData[(int) $row->month - 1] = (int) $row->total;
                }

                $color = $palette[$index % count($palette)];

                $datasets[] = [
                    'label' => $nomConseil,
                    'data' => $monthlyData,
                    'borderColor' => $color,
                    'backgroundColor' => $color . '20', // Opacité légère en hexadécimal
                    'fill' => false,
                    'tension' => 0.4,
                    'pointRadius' => 4,
                ];
            }

            return [
                'labels' => $labels,
                'datasets' => $datasets,
            ];
        }

        // =========================================================================
        // MODE 2 : Cumul progressif sur l'année
        // =========================================================================
        if (str_starts_with($activeFilter, 'cumul_')) {
            $year = (int) str_replace('cumul_', '', $activeFilter);

            $rows = (clone $baseQuery)
                ->selectRaw('MONTH(created_at) as month, Genre, COUNT(*) as total')
                ->whereYear('created_at', $year)
                ->groupByRaw('MONTH(created_at), Genre')
                ->get();

            $hommesMensuel = array_fill(0, 12, 0);
            $femmesMensuel = array_fill(0, 12, 0);
            $totalMensuel = array_fill(0, 12, 0);

            foreach ($rows as $row) {
                $monthIndex = (int) $row->month - 1;
                $count = (int) $row->total;

                if ($row->Genre === 'Masculin') {
                    $hommesMensuel[$monthIndex] += $count;
                } elseif ($row->Genre === 'Féminin') {
                    $femmesMensuel[$monthIndex] += $count;
                }

                $totalMensuel[$monthIndex] += $count;
            }

            // Calcul du cumul progressif mois par mois
            $hommesCumul = [];
            $femmesCumul = [];
            $totalCumul = [];
            $accH = $accF = $accT = 0;

            for ($i = 0; $i < 12; $i++) {
                $accH += $hommesMensuel[$i];
                $accF += $femmesMensuel[$i];
                $accT += $totalMensuel[$i];

                $hommesCumul[] = $accH;
                $femmesCumul[] = $accF;
                $totalCumul[] = $accT;
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => "Total cumulé ($year)",
                        'data' => $totalCumul,
                        'borderColor' => '#10b981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                        'fill' => true,
                        'tension' => 0.4,
                        'pointRadius' => 4,
                    ],
                    [
                        'label' => "Hommes cumulés ($year)",
                        'data' => $hommesCumul,
                        'borderColor' => '#3b82f6',
                        'backgroundColor' => 'transparent',
                        'fill' => false,
                        'tension' => 0.4,
                        'pointRadius' => 3,
                    ],
                    [
                        'label' => "Femmes cumulées ($year)",
                        'data' => $femmesCumul,
                        'borderColor' => '#ec4899',
                        'backgroundColor' => 'transparent',
                        'fill' => false,
                        'tension' => 0.4,
                        'pointRadius' => 3,
                    ],
                ],
            ];
        }

        // =========================================================================
        // MODE 3 : Comparaison Année en cours vs Année précédente
        // =========================================================================
        if ($activeFilter === 'compare_years') {
            $lastYear = $currentYear - 1;

            $rows = (clone $baseQuery)
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
                ->whereIn(DB::raw('YEAR(created_at)'), [$currentYear, $lastYear])
                ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                ->get();

            $dataCurrent = array_fill(0, 12, 0);
            $dataLast = array_fill(0, 12, 0);

            foreach ($rows as $row) {
                $monthIndex = (int) $row->month - 1;
                if ((int) $row->year === $currentYear) {
                    $dataCurrent[$monthIndex] = (int) $row->total;
                } elseif ((int) $row->year === $lastYear) {
                    $dataLast[$monthIndex] = (int) $row->total;
                }
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => "Année $currentYear",
                        'data' => $dataCurrent,
                        'borderColor' => '#10b981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                        'fill' => true,
                        'tension' => 0.4,
                        'pointRadius' => 4,
                    ],
                    [
                        'label' => "Année $lastYear",
                        'data' => $dataLast,
                        'borderColor' => '#6b7280',
                        'backgroundColor' => 'rgba(107, 114, 128, 0.1)',
                        'borderDash' => [5, 5],
                        'fill' => false,
                        'tension' => 0.4,
                        'pointRadius' => 4,
                    ],
                ],
            ];
        }

        // =========================================================================
        // MODE 4 : Par genre (défaut)
        // =========================================================================
        $year = (int) str_replace('genre_', '', $activeFilter);

        $rows = (clone $baseQuery)
            ->selectRaw('MONTH(created_at) as month, Genre, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->whereNotNull('Genre')
            ->groupByRaw('MONTH(created_at), Genre')
            ->get();

        $hommes = array_fill(0, 12, 0);
        $femmes = array_fill(0, 12, 0);

        foreach ($rows as $row) {
            $monthIndex = (int) $row->month - 1;
            $count = (int) $row->total;

            if ($row->Genre === 'Masculin') {
                $hommes[$monthIndex] += $count;
            } elseif ($row->Genre === 'Féminin') {
                $femmes[$monthIndex] += $count;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => "Hommes ($year)",
                    'data' => $hommes,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.15)',
                    'fill' => false,
                    'tension' => 0.4,
                    'pointRadius' => 4,
                ],
                [
                    'label' => "Femmes ($year)",
                    'data' => $femmes,
                    'borderColor' => '#ec4899',
                    'backgroundColor' => 'rgba(236, 72, 153, 0.15)',
                    'fill' => false,
                    'tension' => 0.4,
                    'pointRadius' => 4,
                ],
            ],
        ];
    }
}
