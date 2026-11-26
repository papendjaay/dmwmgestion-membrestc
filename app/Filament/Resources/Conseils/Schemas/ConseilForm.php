<?php

namespace App\Filament\Resources\Conseils\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ConseilForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Carte 1 : Informations du Conseil
                Section::make('Informations du Conseil')
                    ->description('Renseignez le nom officiel en majuscules et le code unique du conseil.')
                    ->icon(Heroicon::OutlinedBuildingOffice)
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 2])
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('Nom')
                                    ->label('NOM')
                                    ->placeholder('NOM CONSEIL')
                                    ->extraInputAttributes(['style' => 'text-transform: uppercase;'])
                                    ->dehydrateStateUsing(fn($state) => mb_strtoupper(trim($state)))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('Code')
                                    ->label('CODE')
                                    ->placeholder('Ex. : C1 ou C2')
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->maxLength(50),
                            ]),
                    ]),

                // Carte 2 : Points Focaux du Conseil (ordonnés sur 3 colonnes)
                Section::make('Points Focaux du Conseil')
                    ->description('Coordonnées des responsables du conseil (un compte utilisateur est créé automatiquement).')
                    ->icon(Heroicon::OutlinedUsers)
                    ->columnSpanFull()
                    ->schema([
                        // --- POINT FOCAL 1 ---
                        Fieldset::make('Point Focal 1 (Principal)')
                            ->schema([
                                Grid::make(['default' => 1, 'lg' => 3])
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('pf1')
                                            ->label('PF1 (Nom complet)')
                                            ->placeholder('Nom Point Focal 1')
                                            ->prefixIcon(Heroicon::OutlinedUser)
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('tel_pf1')
                                            ->label('TEL-PF1')
                                            ->mask('99 999 99 99')
                                            ->placeholder('77 000 00 00')
                                            ->prefixIcon(Heroicon::OutlinedPhone)
                                            ->required()
                                            ->maxLength(30),

                                        TextInput::make('email_pf1')
                                            ->label('EMAIL-PF1 (Gmail)')
                                            ->email()
                                            ->placeholder('exemple@gmail.com')
                                            ->prefixIcon(Heroicon::OutlinedEnvelope)
                                            ->maxLength(255),
                                    ]),
                            ]),

                        // --- POINT FOCAL 2 ---
                        Fieldset::make('Point Focal 2')
                            ->schema([
                                Grid::make(['default' => 1, 'lg' => 3])
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('pf2')
                                            ->label('PF2 (Nom complet)')
                                            ->placeholder('Nom Point Focal 2')
                                            ->prefixIcon(Heroicon::OutlinedUser)
                                            ->maxLength(255),

                                        TextInput::make('tel_pf2')
                                            ->label('TEL-PF2')
                                            ->mask('99 999 99 99')
                                            ->placeholder('77 000 00 00')
                                            ->prefixIcon(Heroicon::OutlinedPhone)
                                            ->maxLength(30),

                                        TextInput::make('email_pf2')
                                            ->label('EMAIL-PF2 (Gmail)')
                                            ->email()
                                            ->placeholder('exemple@gmail.com')
                                            ->prefixIcon(Heroicon::OutlinedEnvelope)
                                            ->maxLength(255),
                                    ]),
                            ]),

                        // --- POINT FOCAL 3 ---
                        Fieldset::make('Point Focal 3')
                            ->schema([
                                Grid::make(['default' => 1, 'lg' => 3])
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('pf3')
                                            ->label('PF3 (Nom complet)')
                                            ->placeholder('Nom Point Focal 3')
                                            ->prefixIcon(Heroicon::OutlinedUser)
                                            ->maxLength(255),

                                        TextInput::make('tel_pf3')
                                            ->label('TEL-PF3')
                                            ->mask('99 999 99 99')
                                            ->placeholder('77 000 00 00')
                                            ->prefixIcon(Heroicon::OutlinedPhone)
                                            ->maxLength(30),

                                        TextInput::make('email_pf3')
                                            ->label('EMAIL-PF3 (Gmail)')
                                            ->email()
                                            ->placeholder('exemple@gmail.com')
                                            ->prefixIcon(Heroicon::OutlinedEnvelope)
                                            ->maxLength(255),
                                    ]),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}
