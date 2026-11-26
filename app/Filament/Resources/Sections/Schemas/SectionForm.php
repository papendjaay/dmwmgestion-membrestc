<?php

namespace App\Filament\Resources\Sections\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    // ÉTAPE 1 : Sélection du Conseil & Informations générales
                    Step::make('Informations générales')
                        ->description('Sélection du conseil et nom')
                        ->icon(Heroicon::OutlinedBuildingOffice2)
                        ->schema([
                            Grid::make(['default' => 1, 'lg' => 3])
                                ->columnSpanFull()
                                ->schema([
                                    Select::make('conseil_id')
                                        ->label('Conseil de rattachement')
                                        ->relationship(
                                            name: 'conseil',
                                            titleAttribute: 'Nom',
                                        )
                                        // Affiche "CODE - NOM" dans la liste déroulante
                                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->Code} - {$record->Nom}")
                                        // Permet de rechercher par Nom ou par Code
                                        ->searchable(['Nom', 'Code'])
                                        ->preload()
                                        ->placeholder('Sélectionnez un conseil...')
                                        ->required()
                                        // Option bonus : permet de créer un conseil rapidement depuis un modal
                                        ->createOptionForm([
                                            TextInput::make('Nom')
                                                ->label('Nom du Conseil')
                                                ->required()
                                                ->maxLength(255),
                                            TextInput::make('Code')
                                                ->label('Code du Conseil')
                                                ->placeholder('Ex. : C1')
                                                ->required()
                                                ->maxLength(50),
                                        ]),

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

                    // ÉTAPE 2 : Point Focal 1
                    Step::make('Point Focal 1')
                        ->description('Responsable principal')
                        ->icon(Heroicon::OutlinedUserCircle)
                        ->schema([
                            Grid::make(['default' => 1, 'lg' => 3])
                                ->columnSpanFull()
                                ->schema([
                                    TextInput::make('PF1')
                                        ->label('Nom complet')
                                        ->placeholder('Prénom et Nom')
                                        ->prefixIcon(Heroicon::OutlinedUser)
                                        ->dehydrateStateUsing(fn($state) => $state ? ucwords(mb_strtolower(trim($state))) : null)
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('TEL_PF1')
                                        ->label('Téléphone')
                                        ->mask('99 999 99 99')
                                        ->placeholder('77 000 00 00')
                                        ->prefixIcon(Heroicon::OutlinedPhone)
                                        ->regex('/^(70|75|76|77|78|33)\s?\d{3}\s?\d{2}\s?\d{2}$/')
                                        ->validationMessages([
                                            'regex' => 'Format invalide (ex. : 77 123 45 67).',
                                        ])
                                        ->required()
                                        ->maxLength(30),

                                    TextInput::make('EMAIL_PF1')
                                        ->label('Email (Gmail)')
                                        ->email()
                                        ->prefixIcon(Heroicon::OutlinedEnvelope)
                                        ->placeholder('exemple@gmail.com'),
                                ]),
                        ]),

                    // ÉTAPE 3 : Point Focal 2
                    Step::make('Point Focal 2')
                        ->description('Deuxième responsable (facultatif)')
                        ->icon(Heroicon::OutlinedUser)
                        ->schema([
                            Grid::make(['default' => 1, 'lg' => 3])
                                ->columnSpanFull()
                                ->schema([
                                    TextInput::make('PF2')
                                        ->label('Nom complet')
                                        ->placeholder('Prénom et Nom')
                                        ->prefixIcon(Heroicon::OutlinedUser)
                                        ->dehydrateStateUsing(fn($state) => $state ? ucwords(mb_strtolower(trim($state))) : null)
                                        ->maxLength(255),

                                    TextInput::make('TEL_PF2')
                                        ->label('Téléphone')
                                        ->mask('99 999 99 99')
                                        ->placeholder('77 000 00 00')
                                        ->prefixIcon(Heroicon::OutlinedPhone)
                                        ->regex('/^(70|75|76|77|78|33)\s?\d{3}\s?\d{2}\s?\d{2}$/')
                                        ->validationMessages([
                                            'regex' => 'Format invalide (ex. : 77 123 45 67).',
                                        ])
                                        ->maxLength(30),

                                    TextInput::make('EMAIL_PF2')
                                        ->label('Email (Gmail)')
                                        ->email()
                                        ->prefixIcon(Heroicon::OutlinedEnvelope)
                                        ->placeholder('exemple@gmail.com'),
                                ]),
                        ]),

                    // ÉTAPE 4 : Point Focal 3
                    Step::make('Point Focal 3')
                        ->description('Troisième responsable (facultatif)')
                        ->icon(Heroicon::OutlinedUser)
                        ->schema([
                            Grid::make(['default' => 1, 'lg' => 3])
                                ->columnSpanFull()
                                ->schema([
                                    TextInput::make('PF3')
                                        ->label('Nom complet')
                                        ->placeholder('Prénom et Nom')
                                        ->prefixIcon(Heroicon::OutlinedUser)
                                        ->dehydrateStateUsing(fn($state) => $state ? ucwords(mb_strtolower(trim($state))) : null)
                                        ->maxLength(255),

                                    TextInput::make('TEL_PF3')
                                        ->label('Téléphone')
                                        ->mask('99 999 99 99')
                                        ->placeholder('77 000 00 00')
                                        ->prefixIcon(Heroicon::OutlinedPhone)
                                        ->regex('/^(70|75|76|77|78|33)\s?\d{3}\s?\d{2}\s?\d{2}$/')
                                        ->validationMessages([
                                            'regex' => 'Format invalide (ex. : 77 123 45 67).',
                                        ])
                                        ->maxLength(30),

                                    TextInput::make('EMAIL_PF3')
                                        ->label('Email (Gmail)')
                                        ->email()
                                        ->prefixIcon(Heroicon::OutlinedEnvelope)
                                        ->placeholder('exemple@gmail.com'),
                                ]),
                        ]),
                ])
                    ->columnSpanFull()
                    ->submitAction(
                        Action::make('create')
                            ->label('Créer la section')
                            ->submit('create')
                            ->color('primary')
                            ->icon(Heroicon::OutlinedCheck)
                    ),
            ]);
    }
}
