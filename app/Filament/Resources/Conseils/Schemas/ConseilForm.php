<?php

namespace App\Filament\Resources\Conseils\Schemas;

use Filament\Forms\Components\TextInput;
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
                // Bloc 1 : Identification du Conseil
                Section::make('Informations du Conseil')
                    ->description('Renseignez le nom officiel en majuscules et le code unique du conseil.')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->schema([
                        TextInput::make('Nom')
                            ->label('NOM')
                            ->placeholder('Nom Conseil')
                            ->extraInputAttributes(['style' => 'text-transform: uppercase;'])
                            ->regex('/^[A-ZÀ-Ÿ\s\-]+$/u')
                            ->validationMessages([
                                'regex' => 'Le nom doit impérativement être écrit en lettres MAJUSCULES (sans chiffres ni caractères spéciaux).',
                            ])
                            ->required(),

                        TextInput::make('Code')
                            ->label('CODE')
                            ->unique(ignoreRecord: true)
                            ->regex('/^C[0-9]{1,2}$/')
                            ->maxLength(3)
                            ->placeholder('Ex. : C1 ou C01')
                            ->validationMessages([
                                'unique' => 'Ce code existe déjà.',
                                'regex' => 'Le code doit commencer par C et être suivi de 1 ou 2 chiffres (ex. : C1 ou C12).',
                            ])
                            ->required(),
                    ])
                    ->columns(2),

                // Bloc 2 : Les 3 Points Focaux et leurs numéros
                Section::make('Points Focaux')
                    ->description('Coordonnées des responsables du conseil.')
                    ->icon(Heroicon::OutlinedUsers)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('PF1')
                                    ->label('PF1')
                                    ->placeholder('Nom Point Focal 1')
                                    ->maxLength(50)
                                    ->dehydrateStateUsing(fn($state) => ucwords(mb_strtolower($state)))
                                    ->regex('/^[A-ZÀ-Ÿ][a-zA-ZÀ-ÿ]*(?:[\s\-][A-ZÀ-Ÿ][a-zA-ZÀ-ÿ]*)*$/u')
                                    ->validationMessages([
                                        'max' => 'Le nom ne doit pas dépasser 50 caractères.',
                                        'regex' => 'Le nom doit commencer par une majuscule.',
                                    ])
                                    ->required(),

                                TextInput::make('tel_pf1')
                                    ->label('TEL-PF1')
                                    ->mask('99 999 99 99')
                                    ->placeholder('77 000 00 00')
                                    ->regex('/^(70|75|76|77|78|33)\s?\d{3}\s?\d{2}\s?\d{2}$/')
                                    ->validationMessages([
                                        'regex' => 'Le format du numéro de téléphone est invalide.',
                                    ])
                                    ->required(),

                                TextInput::make('PF2')
                                    ->label('PF2')
                                    ->placeholder('Nom Point Focal 2')
                                    ->maxLength(50)
                                    ->dehydrateStateUsing(fn($state) => ucwords(mb_strtolower($state)))
                                    ->regex('/^[A-ZÀ-Ÿ][a-zA-ZÀ-ÿ]*(?:[\s\-][A-ZÀ-Ÿ][a-zA-ZÀ-ÿ]*)*$/u')
                                    ->validationMessages([
                                        'max' => 'Le nom ne doit pas dépasser 50 caractères.',
                                        'regex' => 'Le nom doit commencer par une majuscule.',
                                    ])
                                    ->required(),

                                TextInput::make('tel_pf2')
                                    ->label('TEL-PF2')
                                    ->mask('99 999 99 99')
                                    ->placeholder('77 000 00 00')
                                    ->regex('/^(70|75|76|77|78|33)\s?\d{3}\s?\d{2}\s?\d{2}$/')
                                    ->validationMessages([
                                        'regex' => 'Le format du numéro de téléphone est invalide.',
                                    ])
                                    ->required(),

                                TextInput::make('PF3')
                                    ->label('PF3')
                                    ->placeholder('Nom Point Focal 3')
                                    ->maxLength(50)
                                    ->dehydrateStateUsing(fn($state) => ucwords(mb_strtolower($state)))
                                    ->regex('/^[A-ZÀ-Ÿ][a-zA-ZÀ-ÿ]*(?:[\s\-][A-ZÀ-Ÿ][a-zA-ZÀ-ÿ]*)*$/u')
                                    ->validationMessages([
                                        'max' => 'Le nom ne doit pas dépasser 50 caractères.',
                                        'regex' => 'Le nom doit commencer par une majuscule.',
                                    ])
                                    ->required(),

                                TextInput::make('tel_pf3')
                                    ->label('TEL-PF3')
                                    ->mask('99 999 99 99')
                                    ->placeholder('77 000 00 00')
                                    ->regex('/^(70|75|76|77|78|33)\s?\d{3}\s?\d{2}\s?\d{2}$/')
                                    ->validationMessages([
                                        'regex' => 'Le format du numéro de téléphone est invalide.',
                                    ])
                                    ->required(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}
