<?php

namespace App\Filament\Widgets;

use App\Models\Conseil;
use App\Models\Enrolement;
use App\Models\Section;
use App\Models\User;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $user = Auth::user();
        $isAdmin = $user instanceof User && $user->hasRole('super_admin');
        $maSection = ! $isAdmin && $user instanceof User ? $user->getMaSection() : null;

        // Récupération des filtres globaux du Dashboard
        $conseil   = $isAdmin ? ($this->filters['conseil'] ?? null) : $maSection?->conseil?->Nom;
        $section   = $isAdmin ? ($this->filters['section'] ?? null) : $maSection?->Nom;
        $startDate = $this->filters['startDate'] ?? null;
        $endDate   = $this->filters['endDate'] ?? null;

        $enrolementsQuery = Enrolement::query()
            ->when(! $isAdmin, fn($q) => $maSection ? $q->where('Section', $maSection->Nom) : $q->whereRaw('1 = 0'))
            ->when($isAdmin && $conseil, fn($q) => $q->where('Conseil', $conseil))
            ->when($isAdmin && $section, fn($q) => $q->where('Section', $section))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));

        $tendanceEnrolements = $this->getEnrolementsTrend($isAdmin, $section, $conseil);

        // =========================================================================
        // VUE UTILISATEUR DE SECTION
        // =========================================================================
        if (! $isAdmin) {
            return [
                Stat::make('MON CONSEIL', $maSection?->conseil?->Nom ?? 'Non rattaché')
                    ->description('Conseil d\'appartenance')
                    ->descriptionIcon('heroicon-m-building-office-2')
                    ->color('info')
                    ->extraAttributes([
                        'class' => 'border-t-4 border-t-blue-500 shadow-sm hover:shadow-md transition-shadow',
                    ]),

                Stat::make('MA SECTION', $maSection?->Nom ?? 'Non rattachée')
                    ->description('Section assignée')
                    ->descriptionIcon('heroicon-m-map-pin')
                    ->color('success')
                    ->extraAttributes([
                        'class' => 'border-t-4 border-t-emerald-500 shadow-sm hover:shadow-md transition-shadow',
                    ]),

                Stat::make('ENRÔLEMENTS', (clone $enrolementsQuery)->count())
                    ->description('Dossiers enregistrés dans votre section')
                    ->descriptionIcon('heroicon-m-clipboard-document-check')
                    ->chart($tendanceEnrolements)
                    ->color('warning')
                    ->extraAttributes([
                        'class' => 'border-t-4 border-t-amber-500 shadow-sm hover:shadow-md transition-shadow',
                    ]),
            ];
        }

        // =========================================================================
        // VUE SUPER ADMINISTRATEUR (réagit aussi aux filtres Conseil / Section)
        // =========================================================================
        $sectionsQuery = Section::query()
            ->when($conseil, fn($q) => $q->whereHas('conseil', fn($q2) => $q2->where('Nom', $conseil)))
            ->when($section, fn($q) => $q->where('Nom', $section));

        $sectionsActives = (clone $sectionsQuery)->where('status', true)->count();
        $totalSections = (clone $sectionsQuery)->count();

        return [
            Stat::make('CONSEILS', $conseil ? 1 : Conseil::count())
                ->description($conseil ? "Filtre : $conseil" : 'Conseils enregistrés')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info')
                ->extraAttributes([
                    'class' => 'border-t-4 border-t-blue-500 shadow-sm hover:shadow-md transition-shadow',
                ]),

            Stat::make('SECTIONS', "$sectionsActives / $totalSections")
                ->description($section ? "Section : $section" : 'Sections actives')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->extraAttributes([
                    'class' => 'border-t-4 border-t-emerald-500 shadow-sm hover:shadow-md transition-shadow',
                ]),

            Stat::make('MEMBRES', number_format(DB::table('membres')->count(), 0, ',', ' '))
                ->description('Membres inscrits dans la base')
                ->descriptionIcon('heroicon-m-users')
                ->color('danger')
                ->extraAttributes([
                    'class' => 'border-t-4 border-t-rose-500 shadow-sm hover:shadow-md transition-shadow',
                ]),

            Stat::make('ENRÔLEMENTS', number_format((clone $enrolementsQuery)->count(), 0, ',', ' '))
                ->description('Dossiers complétés')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'border-t-4 border-t-amber-500 shadow-sm hover:shadow-md transition-shadow',
                ]),
        ];
    }

    /**
     * Calcule l'évolution quotidienne des enrôlements sur les 7 derniers jours
     */
    protected function getEnrolementsTrend(bool $isAdmin, ?string $sectionNom, ?string $conseilNom): array
    {
        $start = now()->subDays(6)->startOfDay();

        $rows = Enrolement::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->when(! $isAdmin, fn($q) => $sectionNom ? $q->where('Section', $sectionNom) : $q->whereRaw('1 = 0'))
            ->when($isAdmin && $conseilNom, fn($q) => $q->where('Conseil', $conseilNom))
            ->when($isAdmin && $sectionNom, fn($q) => $q->where('Section', $sectionNom))
            ->where('created_at', '>=', $start)
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'date')
            ->toArray();

        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateKey = now()->subDays($i)->format('Y-m-d');
            $trend[] = (int) ($rows[$dateKey] ?? 0);
        }

        return $trend;
    }
}
