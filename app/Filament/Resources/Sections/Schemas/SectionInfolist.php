<?php

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section as InfoSection;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Bloc 1 : Informations générales
                InfoSection::make('Informations générales')
                    ->description('Conseil de rattachement, nom officiel et statut de validation.')
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('conseil.Nom')
                            ->label('Conseil de rattachement')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('Nom')
                            ->label('Nom de la section')
                            ->badge()
                            ->color('warning'),

                        TextEntry::make('status')
                            ->label('Statut de validation')
                            ->badge()
                            ->state(fn($record): bool => in_array($record->status, ['Active', '1', 1, true], true))
                            ->formatStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive')
                            ->icon(fn(bool $state) => $state ? Heroicon::OutlinedCheckCircle : Heroicon::OutlinedXCircle)
                            ->color(fn(bool $state): string => $state ? 'success' : 'danger'),
                    ]),

                // Bloc 2 : Points Focaux & Coordonnées cliquables
                InfoSection::make('Points Focaux & Coordonnées')
                    ->description('Cliquez sur un numéro de téléphone pour lancer l\'appel directement.')
                    ->icon(Heroicon::OutlinedUsers)
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // --- POINT FOCAL 1 ---
                                TextEntry::make('PF1')
                                    ->label('Point focal 1')
                                    ->icon(Heroicon::OutlinedUser)
                                    ->placeholder('Non renseigné')
                                    ->weight('bold'),

                                TextEntry::make('tel_pf1')
                                    ->label('Téléphone PF1')
                                    ->state(fn($record) => $record->tel_pf1 ?? $record->TEL_PF1)
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
                                    ->label('Point focal 2')
                                    ->icon(Heroicon::OutlinedUser)
                                    ->placeholder('Non renseigné')
                                    ->weight('bold'),

                                TextEntry::make('tel_pf2')
                                    ->label('Téléphone PF2')
                                    ->state(fn($record) => $record->tel_pf2 ?? $record->TEL_PF2)
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
                                    ->label('Point focal 3')
                                    ->icon(Heroicon::OutlinedUser)
                                    ->placeholder('Non renseigné')
                                    ->weight('bold'),

                                TextEntry::make('tel_pf3')
                                    ->label('Téléphone PF3')
                                    ->state(fn($record) => $record->tel_pf3 ?? $record->TEL_PF3)
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
            ]);
    }
}
