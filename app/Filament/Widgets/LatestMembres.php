<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Enrolements\Schemas\EnrolementForm;
use App\Models\Enrolement;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\Auth;

class LatestMembres extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Derniers enrôlements enregistrés';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $isAdmin = $user instanceof \App\Models\User && $user->hasRole('super_admin');
        $maSection = ! $isAdmin && $user instanceof \App\Models\User ? $user->getMaSection() : null;

        $conseil = $this->pageFilters['conseil'] ?? null;
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;
        $conseil = $this->pageFilters['conseil'] ?? null;
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        return $table
            ->query(
                fn() => Enrolement::query()
                    ->when(! $isAdmin, fn($q) => $maSection ? $q->where('Section', $maSection->Nom) : $q->whereRaw('1 = 0'))
                    ->when($isAdmin && $conseil, fn($q) => $q->where('Conseil', $conseil))
                    ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                    ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('internal_id')
                    ->label('Matricule')

                    ->color('info'),

                TextColumn::make('Prenom')
                    ->label('Prénom'),

                TextColumn::make('Nom')
                    ->label('Nom'),
                TextColumn::make('Genre')
                    ->label('Genre')
                    ->color(fn(string $state): string => match ($state) {
                        'Masculin' => 'info',
                        'Féminin' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('Age')
                    ->label('Âge')
                    ->suffix(' ans'),
                TextColumn::make('Telephone')
                    ->label('Téléphone'),
                TextColumn::make('Conseil')
                    ->label('Conseil'),
                TextColumn::make('Section')
                    ->label('Section')
                    ->color('draft'),
                TextColumn::make('created_at')
                    ->label('Enrôlé le')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->disabledForm()
                    ->schema(fn(Schema $schema) => EnrolementForm::configure($schema)),

                EditAction::make()
                    ->iconButton()
                    ->schema(fn(Schema $schema) => EnrolementForm::configure($schema)),
            ])
            ->paginated(false);
    }
}
