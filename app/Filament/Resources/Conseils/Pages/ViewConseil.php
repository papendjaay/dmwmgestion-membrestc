<?php

namespace App\Filament\Resources\Conseils\Pages;

use App\Filament\Resources\Conseils\ConseilResource;
use App\Models\Enrolement;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class ViewConseil extends ViewRecord
{
    protected static string $resource = ConseilResource::class;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->schema([
                Grid::make([
                    'default' => 1,
                    'md' => 2,
                ])
                    ->columnSpanFull() // Étire les 2 cartes sur toute la largeur disponible
                    ->schema([
                        // =========================================================
                        // COLONNE GAUCHE : Informations Générales
                        // =========================================================
                        Section::make('Informations Générales')
                            ->description('Identité et statistiques globales du conseil')
                            ->icon('heroicon-o-building-office-2')
                            ->iconColor('primary')
                            ->extraAttributes([
                                'class' => 'border-t-4 border-t-sky-500 shadow-sm h-full',
                            ])
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('Nom')
                                            ->label('NOM DU CONSEIL')
                                            ->weight(FontWeight::Bold)
                                            ->size('lg')
                                            ->color('primary'),

                                        TextEntry::make('Code')
                                            ->label('CODE OFFICIEL')
                                            ->badge()
                                            ->color('warning')
                                            ->size('lg'),

                                        TextEntry::make('sections_count')
                                            ->label('SECTIONS RATTACHÉES')
                                            ->state(fn($record): string => $record->sections()->count() . ' section(s)')
                                            ->icon('heroicon-m-map-pin')
                                            ->badge()
                                            ->color('success'),

                                        TextEntry::make('enrolements_count')
                                            ->label('TOTAL ENRÔLÉS')
                                            ->state(fn($record): string => Enrolement::where('Conseil', $record->Nom)->count() . ' membre(s)')
                                            ->icon('heroicon-m-users')
                                            ->badge()
                                            ->color('info'),

                                        TextEntry::make('created_at')
                                            ->label('DATE DE CRÉATION')
                                            ->icon('heroicon-m-calendar-days')
                                            ->dateTime('d/m/Y')
                                            ->color('gray')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // =========================================================
                        // COLONNE DROITE : Points Focaux & Contacts
                        // =========================================================
                        Section::make('Points Focaux & Contacts')
                            ->description('Responsables désignés — cliquez sur un numéro pour appeler')
                            ->icon('heroicon-o-phone')
                            ->iconColor('success')
                            ->extraAttributes([
                                'class' => 'border-t-4 border-t-emerald-500 shadow-sm h-full',
                            ])
                            ->schema([
                                // Ligne PF1
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('PF1')
                                            ->label('POINT FOCAL 1')
                                            ->icon('heroicon-m-user-circle')
                                            ->iconColor('success')
                                            ->weight(FontWeight::Bold)
                                            ->placeholder('Non renseigné'),

                                        TextEntry::make('tel_pf1')
                                            ->label('TÉLÉPHONE PF1')
                                            ->icon('heroicon-m-phone')
                                            ->url(fn(?string $state): ?string => filled($state) ? 'tel:+221' . preg_replace('/\s+/', '', $state) : null)
                                            ->badge()
                                            ->color('info')
                                            ->placeholder('Non renseigné'),
                                    ]),

                                // Ligne PF2
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('PF2')
                                            ->label('POINT FOCAL 2')
                                            ->icon('heroicon-m-user-circle')
                                            ->iconColor('success')
                                            ->weight(FontWeight::Bold)
                                            ->placeholder('Non renseigné'),

                                        TextEntry::make('tel_pf2')
                                            ->label('TÉLÉPHONE PF2')
                                            ->icon('heroicon-m-phone')
                                            ->url(fn(?string $state): ?string => filled($state) ? 'tel:+221' . preg_replace('/\s+/', '', $state) : null)
                                            ->badge()
                                            ->color('info')
                                            ->placeholder('Non renseigné'),
                                    ]),

                                // Ligne PF3
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('PF3')
                                            ->label('POINT FOCAL 3')
                                            ->icon('heroicon-m-user-circle')
                                            ->iconColor('success')
                                            ->weight(FontWeight::Bold)
                                            ->placeholder('Non renseigné'),

                                        TextEntry::make('tel_pf3')
                                            ->label('TÉLÉPHONE PF3')
                                            ->icon('heroicon-m-phone')
                                            ->url(fn(?string $state): ?string => filled($state) ? 'tel:+221' . preg_replace('/\s+/', '', $state) : null)
                                            ->badge()
                                            ->color('info')
                                            ->placeholder('Non renseigné'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Retour')
                ->url(ConseilResource::getUrl('index'))
                ->color('gray')
                ->outlined()
                ->icon('heroicon-m-arrow-left'),

            EditAction::make()
                ->label('Modifier le conseil')
                ->icon('heroicon-m-pencil-square'),
        ];
    }
}
