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

    protected function getStats(): array
    {
        $user = Auth::user();
        $isAdmin = $user instanceof User && $user->hasRole('super_admin');
        $maSection = ! $isAdmin && $user instanceof User ? $user->getMaSection() : null;

        $conseil = $isAdmin ? ($this->pageFilters['conseil'] ?? null) : $maSection?->conseil?->Nom;
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        $enrolementsQuery = Enrolement::query()
            ->when(! $isAdmin, fn($q) => $maSection ? $q->where('Section', $maSection->Nom) : $q->whereRaw('1 = 0'))
            ->when($isAdmin && $conseil, fn($q) => $q->where('Conseil', $conseil))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));

        // Génère une courbe de tendance réelle sur les 7 derniers jours
        $tendanceEnrolements = $this->getEnrolementsTrend($isAdmin, $maSection?->Nom, $conseil);

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
        // VUE SUPER ADMINISTRATEUR
        // =========================================================================
        $sectionsActives = Section::where('status', true)->count();
        $totalSections = Section::count();

        return [
            Stat::make('CONSEILS', $conseil ? 1 : Conseil::count())
                ->description($conseil ? "Filtre : $conseil" : 'Conseils enregistrés')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info')
                ->extraAttributes([
                    'class' => 'border-t-4 border-t-blue-500 shadow-sm hover:shadow-md transition-shadow',
                ]),

            Stat::make('SECTIONS', "$sectionsActives / $totalSections")
                ->description('Sections actives sur le territoire')
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
                // ->chart($tendanceEnrolements)
                ->color('warning')
                ->extraAttributes([
                    'class' => 'border-t-4 border-t-amber-500 shadow-sm hover:shadow-md transition-shadow',
                ]),
        ];
    }
    protected int | string | array $columnSpan = 'full';

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
