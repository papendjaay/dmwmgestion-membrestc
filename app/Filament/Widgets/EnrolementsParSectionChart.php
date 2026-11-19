<?php

namespace App\Filament\Widgets;

use App\Models\Enrolement;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;

class EnrolementsParSectionChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;
    protected ?string $maxHeight = '380px';

    public function getHeading(): ?string
    {
        $user = Auth::user();
        $isAdmin = $user instanceof User && $user->hasRole('super_admin');

        return $isAdmin ? 'Répartition par Section' : 'Répartition par Adresse / Quartier';
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        return [
            'top10' => 'Top 10',
            'top20' => 'Top 20',
            'all'   => 'Tout afficher',
        ];
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $isAdmin = $user instanceof User && $user->hasRole('super_admin');
        $maSection = ! $isAdmin && $user instanceof User ? $user->getMaSection() : null;

        // Colonne de regroupement dynamique selon le rôle
        $groupColumn = $isAdmin ? 'Section' : 'Adresse';

        // Récupération des filtres globaux du Dashboard
        $conseil   = $this->filters['conseil'] ?? null;
        $section   = $this->filters['section'] ?? null;
        $startDate = $this->filters['startDate'] ?? null;
        $endDate   = $this->filters['endDate'] ?? null;

        $limitFilter = $this->filter ?? 'top10';

        // 1. Récupère les catégories triées par effectif total décroissant
        $topQuery = Enrolement::query()
            ->selectRaw("{$groupColumn} as label_name, COUNT(*) as total")
            ->whereNotNull($groupColumn)
            ->where($groupColumn, '!=', '')
            ->when(! $isAdmin, fn($q) => $maSection ? $q->where('Section', $maSection->Nom) : $q->whereRaw('1 = 0'))
            ->when($isAdmin && $conseil, fn($q) => $q->where('Conseil', $conseil))
            ->when($isAdmin && $section, fn($q) => $q->where('Section', $section))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->groupBy($groupColumn)
            ->orderByDesc('total');

        if ($limitFilter === 'top10') {
            $topQuery->limit(10);
        } elseif ($limitFilter === 'top20') {
            $topQuery->limit(20);
        }

        $labelsList = $topQuery->pluck('label_name')->toArray();

        if (empty($labelsList)) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }

        // 2. Récupère le détail Hommes / Femmes pour ces catégories
        $details = Enrolement::query()
            ->selectRaw("{$groupColumn} as label_name, Genre, COUNT(*) as total")
            ->whereIn($groupColumn, $labelsList)
            ->when(! $isAdmin, fn($q) => $maSection ? $q->where('Section', $maSection->Nom) : $q->whereRaw('1 = 0'))
            ->when($isAdmin && $conseil, fn($q) => $q->where('Conseil', $conseil))
            ->when($isAdmin && $section, fn($q) => $q->where('Section', $section))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->groupBy($groupColumn, 'Genre')
            ->get();

        $hommesData = [];
        $femmesData = [];

        foreach ($labelsList as $labelName) {
            $hommesData[] = (int) ($details->where('label_name', $labelName)->where('Genre', 'Masculin')->first()?->total ?? 0);
            $femmesData[] = (int) ($details->where('label_name', $labelName)->where('Genre', 'Féminin')->first()?->total ?? 0);
        }

        return [
            'labels' => $labelsList,
            'datasets' => [
                [
                    'label' => 'Hommes',
                    'data' => $hommesData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.85)',
                    'borderColor' => '#2563eb',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Femmes',
                    'data' => $femmesData,
                    'backgroundColor' => 'rgba(236, 72, 153, 0.85)',
                    'borderColor' => '#db2777',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 15,
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
                'y' => [
                    'stacked' => true,
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        return true;
    }
}
