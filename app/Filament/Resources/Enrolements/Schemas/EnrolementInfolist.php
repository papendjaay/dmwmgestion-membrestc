<?php

namespace App\Filament\Resources\Enrolements\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EnrolementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité & Profil')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->columns(4)
                    ->schema([
                        TextEntry::make('internal_id')
                            ->label('MATRICULE')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('Prenom')
                            ->label('PRÉNOM(S)')
                            ->weight('bold'),

                        TextEntry::make('Nom')
                            ->label('NOM')
                            ->weight('bold'),

                        IconEntry::make('Statut')
                            ->label('STATUT MEMBRE')
                            ->state(fn($record): bool => in_array(strtolower(trim((string) $record->Statut)), ['actif', 'activé', 'active', '1', 'true', 'oui'], true))
                            ->boolean()
                            ->trueColor('success')
                            ->falseColor('danger'),

                        TextEntry::make('Genre')
                            ->label('GENRE')
                            ->badge(),

                        TextEntry::make('Age')
                            ->label('ÂGE')
                            ->suffix(' ans')
                            ->placeholder('Non renseigné'),

                        TextEntry::make('Niveau_Etude')
                            ->label('NIVEAU D\'ÉTUDE')
                            ->placeholder('Non renseigné'),

                        TextEntry::make('Profession')
                            ->label('PROFESSION')
                            ->placeholder('Non renseigné'),
                    ]),

                Section::make('Rattachement & Coordonnées')
                    ->icon(Heroicon::OutlinedMapPin)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('Conseil')
                            ->label('CONSEIL')
                            ->badge()
                            ->color('warning'),

                        TextEntry::make('Section')
                            ->label('SECTION')
                            ->badge()
                            ->color('success'),

                        TextEntry::make('Telephone')
                            ->label('TÉLÉPHONE')
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

                        TextEntry::make('Adresse')
                            ->label('ADRESSE')
                            ->placeholder('Non renseignée')
                            ->columnSpanFull(),
                    ]),

                Section::make('Identification & Contact d\'urgence')
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('Type_piece_identite')
                                    ->label('TYPE DE PIÈCE')
                                    ->placeholder('Non renseigné'),

                                TextEntry::make('Numero_piece_identite')
                                    ->label('NUMÉRO DE PIÈCE')
                                    ->placeholder('Non renseigné'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('Parent')
                                    ->label('PARENT / TUTEUR')
                                    ->placeholder('Non renseigné'),

                                TextEntry::make('Lien_parental')
                                    ->label('LIEN DE PARENTALITÉ')
                                    ->placeholder('Non renseigné'),

                                TextEntry::make('Contact')
                                    ->label('TÉLÉPHONE DU PROCHE')
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

                Section::make('Traçabilité')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('Cree_par')
                            ->label('ENRÔLÉ PAR')
                            ->placeholder('Système'),

                        TextEntry::make('created_at')
                            ->label('DATE DE CRÉATION')
                            ->dateTime('d/m/Y à H:i'),

                        TextEntry::make('updated_at')
                            ->label('DERNIÈRE MODIFICATION')
                            ->dateTime('d/m/Y à H:i'),
                    ]),
            ])
            ->columns(1);
    }
}
