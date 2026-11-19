<?php

namespace App\Filament\Widgets;

use App\Models\Enrolement;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;

class MembresAgeChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Effectif des Enrôlés par Tranche d\'Âge';
    protected static ?int $sort = 3;
    protected ?string $maxHeight = '380px';

    protected function getFilters(): ?array
    {
        $user = Auth::user();
        $isAdmin = $user instanceof User && $user->hasRole('super_admin');

        if (! $isAdmin) {
            return [
                'all'      => 'Ma Section (Tous)',
                'Masculin' => 'Ma Section (Hommes)',
                'Féminin'  => 'Ma Section (Femmes)',
            ];
        }

        return [
            'all'      => 'Tous les genres',
            'Masculin' => 'Hommes',
            'Féminin'  => 'Femmes',
        ];
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $isAdmin = $user instanceof User && $user->hasRole('super_admin');
        $maSection = ! $isAdmin && $user instanceof User ? $user->getMaSection() : null;

        $genreFilter = $this->filter ?? 'all';

        // Filtres globaux du Dashboard
        $conseil   = $this->filters['conseil'] ?? null;
        $section   = $this->filters['section'] ?? null;
        $startDate = $this->filters['startDate'] ?? null;
        $endDate   = $this->filters['endDate'] ?? null;

        $query = Enrolement::query()
            ->whereNotNull('Age')
            ->when(! $isAdmin, fn($q) => $maSection ? $q->where('Section', $maSection->Nom) : $q->whereRaw('1 = 0'))
            ->when($isAdmin && $conseil, fn($q) => $q->where('Conseil', $conseil))
            ->when($isAdmin && $section, fn($q) => $q->where('Section', $section))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->when($genreFilter !== 'all', fn($q) => $q->where('Genre', $genreFilter));

        $enrolements = $query->get(['Age']);

        $tranches = [
            'Moins de 18 ans' => 0,
            '18 - 35 ans'      => 0,
            '36 - 50 ans'      => 0,
            'Plus de 50 ans'   => 0,
        ];

        foreach ($enrolements as $enrole) {
            $age = (int) $enrole->Age;

            if ($age < 18) {
                $tranches['Moins de 18 ans']++;
            } elseif ($age <= 35) {
                $tranches['18 - 35 ans']++;
            } elseif ($age <= 50) {
                $tranches['36 - 50 ans']++;
            } else {
                $tranches['Plus de 50 ans']++;
            }
        }

        return [
            'labels' => array_keys($tranches),
            'datasets' => [
                [
                    'label' => 'Effectif des Enrôlés',
                    'data' => array_values($tranches),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.85)',
                        'rgba(16, 185, 129, 0.85)',
                        'rgba(245, 158, 11, 0.85)',
                        'rgba(239, 68, 68, 0.85)',
                    ],
                    'borderColor' => [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                    ],
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
                'r' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
            ],
        ];
    }
}
