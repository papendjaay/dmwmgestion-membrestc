<?php

namespace App\Filament\Resources\Membres\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MembresForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    // PREMIÈRE ÉTAPE (Page 1) : Les 10 premières informations
                    Step::make("Informations Personnelles")
                        ->icon(Heroicon::Identification)
                        ->description("Infos")
                        ->schema([
                            TextInput::make('internal_id')
                                ->label('Matricule')
                                ->placeholder('MTC' . date('y') . '-XXX (Généré automatiquement)')
                                ->disabled()
                                ->dehydrated(false)
                                ->visible(fn($record) => $record !== null),

                            TextInput::make('Prenom')
                                ->placeholder('Prénom')
                                ->required(),

                            TextInput::make('Nom')
                                ->placeholder('Nom')
                                ->required(),

                            Select::make('Genre')
                                ->options([
                                    'Masculin' => 'Masculin',
                                    'Féminin' => 'Féminin',
                                ])
                                ->placeholder('Genre')
                                ->searchable()
                                ->required(),

                            // 1. On ajoute live() pour écouter les changements d'âge en temps réel
                            TextInput::make('Age')
                                ->placeholder('Ex: 25')
                                ->numeric()
                                ->minValue(0)
                                ->required()
                                ->live(onBlur: true) // S'active dès qu'on quitte le champ de saisie
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // Si l'âge est inférieur à 18, on force le type et le numéro de pièce
                                    if (filled($state) && (int) $state < 18) {
                                        $set('Type_piece_identite', 'PAS DE PIECE');
                                        $set('Numero_piece_identite', 'PAS ENCORE DE PIECE');
                                    } else {
                                        // On réinitialise si la personne repasse au-dessus de 18 ans
                                        $set('Type_piece_identite', null);
                                        $set('Numero_piece_identite', null);
                                    }
                                }),

                            TextInput::make('Niveau_Etude')
                                ->placeholder('Niveau Etude')
                                ->required(),

                            TextInput::make('Profession')
                                ->placeholder('Profession')
                                ->required(),

                            TextInput::make('Telephone')
                                ->tel()
                                ->mask('99 999 99 99')
                                ->placeholder('77 000 00 00')
                                ->regex('/^(70|75|76|77|78|33)\s?\d{3}\s?\d{2}\s?\d{2}$/')
                                ->validationMessages([
                                    'regex' => 'Le format du numéro de téléphone est invalide.',
                                ])
                                ->placeholder('Numero Téléphone'),

                            TextInput::make('Adresse')
                                ->placeholder('Adresse')
                                ->required(),

                            // 2. Gestion dynamique du Type de pièce
                            Select::make('Type_piece_identite')
                                ->options([
                                    'CNI' => 'CNI',
                                    'PASSPORT' => 'PASSPORT',
                                    'PAS DE PIECE' => 'PAS ENCORE DE PIECE',
                                ])
                                ->searchable()
                                ->required()
                                // Désactivé si l'âge est inférieur à 18 ans
                                ->disabled(fn($get) => filled($get('Age')) && (int) $get('Age') < 18)
                                ->dehydrated(), // Permet d'envoyer la valeur en BDD même si le champ est disabled

                            // 3. Gestion dynamique du Numéro de pièce
                            TextInput::make('Numero_piece_identite')
                                ->placeholder('Numero Pièce')
                                ->maxLength(13)
                                ->minLength(13)
                                ->unique(ignoreRecord: true)
                                ->validationMessages([
                                    'digits' => 'Le numéro de carte doit contenir exactement 13 chiffres.',
                                    'required' => 'Le numéro de carte est obligatoire.',
                                ])
                                // Le champ requis uniquement si l'âge est >= 18 ans
                                ->required(fn($get) => blank($get('Age')) || (int) $get('Age') >= 18)
                                // Le champ est masqué/invisible si l'âge est inférieur à 18 ans
                                ->hidden(fn($get) => filled($get('Age')) && (int) $get('Age') < 18),
                        ])
                        ->columns(2),

                    // SECONDE ÉTAPE (Page 2) : Le reste des informations
                    Step::make("Informations Complémentaires")
                        ->icon(Heroicon::InformationCircle)
                        ->description("Infos")
                        ->schema([
                            Select::make('Conseil')
                                ->options([
                                    'oriental' => 'Oriental',
                                    'koussanar' => 'Koussanar',
                                    'boundou' => 'Boundou',
                                ])
                                ->searchable()
                                ->placeholder('Conseil')
                                ->required(),

                            TextInput::make('Section')
                                ->placeholder('Nom section')
                                ->required(),

                            TextInput::make('Parent')
                                ->placeholder('Nom Complet')
                                ->required(),

                            TextInput::make('Lien_parental')
                                ->placeholder('Lien parental')
                                ->required(),

                            TextInput::make('Contact')
                                ->tel()
                                ->mask('99 999 99 99')
                                ->placeholder('77 000 00 00')
                                ->regex('/^(70|75|76|77|78|33)\s?\d{3}\s?\d{2}\s?\d{2}$/')
                                ->validationMessages([
                                    'regex' => 'Le format du numéro de téléphone est invalide.',
                                ])
                                ->placeholder('Numero Téléphone')
                                ->required(),

                            Select::make('Cree_par')
                                ->label('Créé par')
                                ->options([
                                    1 => 'PF1',
                                    2 => 'PF2',
                                    3 => 'PF3',
                                ])
                                ->required()
                                ->searchable(),

                            Toggle::make('Statut')
                                ->label('Statut du membre')
                                ->onColor('success')
                                ->offColor('danger')
                                ->onIcon('heroicon-m-check-circle')
                                ->offIcon('heroicon-m-x-circle')
                                ->default(true)
                                ->required(),
                        ])
                        ->columns(2)
                ])
                    ->columnSpanFull()
                    ->skippable()
                // ->submitAction(
                //     Action::make("save")
                //         ->label("Enregistrer")
                //         ->button()
                //         ->color("primary")
                //         ->submit("save")
                // )
            ]);
    }
}
