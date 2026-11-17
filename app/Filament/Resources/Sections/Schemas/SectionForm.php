<?php

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section as FormSection;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Bloc 1 : Informations générales
                FormSection::make('Informations générales')
                    ->description('Rattachement au conseil, nom officiel et statut de validation de la section.')
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('conseil_id')
                                    ->label('Conseil de rattachement')
                                    ->relationship('conseil', 'Nom')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                TextInput::make('Nom')
                                    ->label('Nom de la Section')
                                    ->placeholder('Ex. : Abattoirs')
                                    ->dehydrateStateUsing(fn($state) => ucwords(mb_strtolower(trim($state))))
                                    ->required()
                                    ->maxLength(255),

                                Toggle::make('status')
                                    ->label('Section validée')
                                    ->helperText('Activez pour valider la section')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->formatStateUsing(fn($state): bool => in_array($state, ['Active', '1', 1, true], true))
                                    ->dehydrateStateUsing(fn(bool $state): string => $state ? 'Active' : 'Inactive')
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ]),

                // Bloc 2 : Points Focaux & Téléphones
                FormSection::make('Points Focaux & Coordonnées')
                    ->description('Renseignez les responsables de la section et leurs numéros de téléphone.')
                    ->icon(Heroicon::OutlinedUsers)
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // --- POINT FOCAL 1 ---
                                TextInput::make('PF1')
                                    ->label('Point Focal 1 (Nom complet)')
                                    ->placeholder('Prénom et Nom')
                                    ->prefixIcon(Heroicon::OutlinedUser)
                                    ->dehydrateStateUsing(fn($state) => $state ? ucwords(mb_strtolower(trim($state))) : null)
                                    ->maxLength(255),

                                TextInput::make('TEL_PF1')
                                    ->label('Téléphone PF1')
                                    ->mask('99 999 99 99')
                                    ->placeholder('77 000 00 00')
                                    ->prefixIcon(Heroicon::OutlinedPhone)
                                    ->regex('/^(70|75|76|77|78|33)\s?\d{3}\s?\d{2}\s?\d{2}$/')
                                    ->validationMessages([
                                        'regex' => 'Format invalide (ex. : 77 123 45 67).',
                                    ])
                                    ->maxLength(30),

                                // --- POINT FOCAL 2 ---
                                TextInput::make('PF2')
                                    ->label('Point Focal 2 (Nom complet)')
                                    ->placeholder('Prénom et Nom')
                                    ->prefixIcon(Heroicon::OutlinedUser)
                                    ->dehydrateStateUsing(fn($state) => $state ? ucwords(mb_strtolower(trim($state))) : null)
                                    ->maxLength(255),

                                TextInput::make('TEL_PF2')
                                    ->label('Téléphone PF2')
                                    ->mask('99 999 99 99')
                                    ->placeholder('77 000 00 00')
                                    ->prefixIcon(Heroicon::OutlinedPhone)
                                    ->regex('/^(70|75|76|77|78|33)\s?\d{3}\s?\d{2}\s?\d{2}$/')
                                    ->validationMessages([
                                        'regex' => 'Format invalide (ex. : 77 123 45 67).',
                                    ])
                                    ->maxLength(30),

                                // --- POINT FOCAL 3 ---
                                TextInput::make('PF3')
                                    ->label('Point Focal 3 (Nom complet)')
                                    ->placeholder('Prénom et Nom')
                                    ->prefixIcon(Heroicon::OutlinedUser)
                                    ->dehydrateStateUsing(fn($state) => $state ? ucwords(mb_strtolower(trim($state))) : null)
                                    ->maxLength(255),

                                TextInput::make('TEL_PF3')
                                    ->label('Téléphone PF3')
                                    ->mask('99 999 99 99')
                                    ->placeholder('77 000 00 00')
                                    ->prefixIcon(Heroicon::OutlinedPhone)
                                    ->regex('/^(70|75|76|77|78|33)\s?\d{3}\s?\d{2}\s?\d{2}$/')
                                    ->validationMessages([
                                        'regex' => 'Format invalide (ex. : 77 123 45 67).',
                                    ])
                                    ->maxLength(30),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}
