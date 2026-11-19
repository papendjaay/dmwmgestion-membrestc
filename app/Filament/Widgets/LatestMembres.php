<?php

namespace App\Filament\Widgets;

use App\Models\Enrolement;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LatestMembres extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Derniers enrôlements enregistrés';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $isAdmin = $user instanceof User && $user->hasRole('super_admin');
        $maSection = ! $isAdmin && $user instanceof User ? $user->getMaSection() : null;

        // Récupération propre des filtres globaux du Dashboard
        $conseil   = $this->filters['conseil'] ?? null;
        $section   = $this->filters['section'] ?? null;
        $startDate = $this->filters['startDate'] ?? null;
        $endDate   = $this->filters['endDate'] ?? null;

        return $table
            ->query(
                fn() => Enrolement::query()
                    ->when(! $isAdmin, fn($q) => $maSection ? $q->where('Section', $maSection->Nom) : $q->whereRaw('1 = 0'))
                    ->when($isAdmin && $conseil, fn($q) => $q->where('Conseil', $conseil))
                    ->when($isAdmin && $section, fn($q) => $q->where('Section', $section))
                    ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                    ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                    ->latest()
                    ->limit(3)
            )
            ->columns([
                TextColumn::make('internal_id')
                    ->label('Matricule')
                    ->color('info')
                    ->size('sm'),

                TextColumn::make('Prenom')
                    ->label('Nom complet')
                    ->formatStateUsing(fn($record) => trim("{$record->Prenom} {$record->Nom}"))
                    ->weight('medium')
                    ->size('sm'),

                TextColumn::make('Genre')
                    ->label('Genre')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Masculin' => 'info',
                        'Féminin' => 'warning',
                        default => 'gray',
                    })
                    ->size('sm'),

                TextColumn::make('Age')
                    ->label('Âge')
                    ->suffix(' ans')
                    ->size('sm'),

                TextColumn::make('Telephone')
                    ->label('Téléphone')
                    ->size('sm'),

                TextColumn::make('Section')
                    ->label('Section / Conseil')
                    ->description(fn($record) => $record->Conseil)
                    ->size('sm'),

                TextColumn::make('created_at')
                    ->label('Enrôlé le')
                    ->dateTime('d/m/Y H:i')
                    ->size('sm'),
            ])
            ->paginated(false);
    }
}
