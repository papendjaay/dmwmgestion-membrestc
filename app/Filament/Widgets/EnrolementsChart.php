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
    protected ?string $maxHeight = '380px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }

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

        // Filtres globaux du Dashboard
        $conseil   = $this->filters['conseil'] ?? null;
        $section   = $this->filters['section'] ?? null;
        $startDate = $this->filters['startDate'] ?? null;
        $endDate   = $this->filters['endDate'] ?? null;

        $currentYear = now()->year;
        $activeFilter = $this->filter ?? "genre_$currentYear";

        $baseQuery = Enrolement::query()
            ->when(! $isAdmin, fn($q) => $maSection ? $q->where('Section', $maSection->Nom) : $q->whereRaw('1 = 0'))
            ->when($isAdmin && $conseil, fn($q) => $q->where('Conseil', $conseil))
            ->when($isAdmin && $section, fn($q) => $q->where('Section', $section))
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

            $conseilsEnrolements = $rows->pluck('Conseil')->unique()->toArray();
            $tousConseils = array_values(array_unique(array_merge($conseils, $conseilsEnrolements)));

            $palette = [
                '#3b82f6',
                '#10b981',
                '#f59e0b',
                '#ef4444',
                '#8b5cf6',
                '#ec4899',
                '#06b6d4',
                '#84cc16',
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
                    'backgroundColor' => $color . '20',
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

            $currentData = array_fill(0, 12, 0);
            $lastData = array_fill(0, 12, 0);

            foreach ($rows as $row) {
                $monthIndex = (int) $row->month - 1;
                if ((int) $row->year === $currentYear) {
                    $currentData[$monthIndex] = (int) $row->total;
                } elseif ((int) $row->year === $lastYear) {
                    $lastData[$monthIndex] = (int) $row->total;
                }
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => "Année $currentYear",
                        'data' => $currentData,
                        'borderColor' => '#3b82f6',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.15)',
                        'fill' => true,
                        'tension' => 0.4,
                        'pointRadius' => 4,
                    ],
                    [
                        'label' => "Année $lastYear",
                        'data' => $lastData,
                        'borderColor' => '#9ca3af',
                        'backgroundColor' => 'transparent',
                        'borderDash' => [5, 5],
                        'fill' => false,
                        'tension' => 0.4,
                        'pointRadius' => 3,
                    ],
                ],
            ];
        }
        // =========================================================================
        // MODE 4 (Défaut) : Par Genre sur l'année en cours
        // =========================================================================
        $rows = (clone $baseQuery)
            ->selectRaw('MONTH(created_at) as month, Genre, COUNT(*) as total')
            ->whereYear('created_at', $currentYear)
            ->groupByRaw('MONTH(created_at), Genre')
            ->get();
        $hommesData = array_fill(0, 12, 0);
        $femmesData = array_fill(0, 12, 0);
        foreach ($rows as $row) {
            $monthIndex = (int) $row->month - 1;
            if ($row->Genre === 'Masculin') {
                $hommesData[$monthIndex] = (int) $row->total;
            } elseif ($row->Genre === 'Féminin') {
                $femmesData[$monthIndex] = (int) $row->total;
            }
        }
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => "Hommes ($currentYear)",
                    'data' => $hommesData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 4,
                ],
                [
                    'label' => "Femmes ($currentYear)",
                    'data' => $femmesData,
                    'borderColor' => '#ec4899',
                    'backgroundColor' => 'rgba(236, 72, 153, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 4,
                ],
            ],
        ];
    }
}
