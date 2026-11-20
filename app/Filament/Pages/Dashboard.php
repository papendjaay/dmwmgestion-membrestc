<?php

namespace App\Filament\Pages;

use App\Models\Conseil;
use App\Models\Section as SectionModel;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 3,
        ];
    }
    public function getHeading(): string
    {
        return '';
    }
    public function filtersForm(Schema $schema): Schema
    {
        $user = Auth::user();
        $isAdmin = $user instanceof User && $user->hasRole('super_admin');

        return $schema
            ->components([
                Section::make('Filtres')
                    ->icon('heroicon-m-funnel')
                    ->compact()
                    ->collapsible()
                    ->visible($isAdmin) // Masque la section pour les Points Focaux qui n'ont plus de filtre
                    ->schema([
                        Select::make('conseil')
                            ->hiddenLabel()
                            ->placeholder('Tous les conseils')
                            ->options(fn() => Conseil::whereNotNull('Nom')->pluck('Nom', 'Nom'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('section', null)),

                        Select::make('section')
                            ->hiddenLabel()
                            ->placeholder('Toutes les sections')
                            ->options(function (Get $get) {
                                $conseilNom = $get('conseil');

                                return SectionModel::query()
                                    ->whereNotNull('Nom')
                                    ->when($conseilNom, fn($q) => $q->whereHas('conseil', fn($q2) => $q2->where('Nom', $conseilNom)))
                                    ->pluck('Nom', 'Nom');
                            })
                            ->searchable()
                            ->preload(),
                    ])
                    ->headerActions([
                        Action::make('resetFilters')
                            ->label('Réinitialiser')
                            ->icon('heroicon-m-arrow-path')
                            ->color('gray')
                            ->size('xs')
                            ->action(fn() => $this->filters = []),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
