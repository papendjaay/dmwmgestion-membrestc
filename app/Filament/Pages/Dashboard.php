<?php

namespace App\Filament\Pages;

use App\Models\Conseil;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        $user = Auth::user();
        $isAdmin = $user instanceof User && $user->hasRole('super_admin');

        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('conseil')
                            ->label('Filtrer par Conseil')
                            ->options(fn() => Conseil::whereNotNull('Nom')->pluck('Nom', 'Nom')->toArray())
                            ->placeholder('Tous les conseils')
                            ->searchable()
                            ->visible($isAdmin),

                        DatePicker::make('startDate')
                            ->label('Date de début'),

                        DatePicker::make('endDate')
                            ->label('Date de fin'),
                    ])
                    ->columns($isAdmin ? 3 : 2),
            ]);
    }
}
