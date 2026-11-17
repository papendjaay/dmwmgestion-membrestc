<?php

namespace App\Filament\Resources\Conseils\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ConseilInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Bloc 1 : Informations générales du Conseil
                Section::make('Informations du Conseil')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('Nom')
                            ->label('NOM DU CONSEIL')
                            ->badge()
                            ->color('warning'),

                        TextEntry::make('Code')
                            ->label('CODE')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('sections_count')
                            ->label('NOMBRE DE SECTIONS')
                            ->state(fn($record): int => $record->sections()->count())
                            ->badge()
                            ->color('success'),
                    ]),

                // Bloc 2 : Détails des 3 Points Focaux et leurs téléphones cliquables
                Section::make('Points Focaux & Coordonnées')
                    ->description('Cliquez sur un numéro de téléphone pour lancer l\'appel directement.')
                    ->icon(Heroicon::OutlinedUsers)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // --- POINT FOCAL 1 ---
                                TextEntry::make('PF1')
                                    ->label('POINT FOCAL 1')
                                    ->icon(Heroicon::OutlinedUser)
                                    ->placeholder('Non renseigné')
                                    ->weight('bold'),

                                TextEntry::make('tel_pf1')
                                    ->label('TÉLÉPHONE PF1')
                                    ->url(function ($state): ?string {
                                        if (blank($state)) {
                                            return null;
                                        }
                                        $numero = preg_replace('/\s+/', '', $state);
                                        return "tel:+221{$numero}";
                                    })
                                    ->icon(Heroicon::OutlinedPhone)
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('Non renseigné'),

                                // --- POINT FOCAL 2 ---
                                TextEntry::make('PF2')
                                    ->label('POINT FOCAL 2')
                                    ->icon(Heroicon::OutlinedUser)
                                    ->placeholder('Non renseigné')
                                    ->weight('bold'),

                                TextEntry::make('tel_pf2')
                                    ->label('TÉLÉPHONE PF2')
                                    ->url(function ($state): ?string {
                                        if (blank($state)) {
                                            return null;
                                        }
                                        $numero = preg_replace('/\s+/', '', $state);
                                        return "tel:+221{$numero}";
                                    })
                                    ->icon(Heroicon::OutlinedPhone)
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('Non renseigné'),

                                // --- POINT FOCAL 3 ---
                                TextEntry::make('PF3')
                                    ->label('POINT FOCAL 3')
                                    ->icon(Heroicon::OutlinedUser)
                                    ->placeholder('Non renseigné')
                                    ->weight('bold'),

                                TextEntry::make('tel_pf3')
                                    ->label('TÉLÉPHONE PF3')
                                    ->url(function ($state): ?string {
                                        if (blank($state)) {
                                            return null;
                                        }
                                        $numero = preg_replace('/\s+/', '', $state);
                                        return "tel:+221{$numero}";
                                    })
                                    ->icon(Heroicon::OutlinedPhone)
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('Non renseigné'),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}
