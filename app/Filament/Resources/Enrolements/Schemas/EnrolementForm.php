<?php

namespace App\Filament\Resources\Enrolements\Schemas;

use App\Models\Conseil;
use App\Models\Section as SectionModel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class EnrolementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    // =========================================================
                    // ÉTAPE 1 : INFORMATIONS PERSONNELLES (2 COLONNES ÉQUILIBRÉES)
                    // =========================================================
                    Step::make('Informations personnelles')
                        ->description('Identité, coordonnées et profil')
                        ->icon(Heroicon::OutlinedUserCircle)
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('Prenom')
                                        ->label('Prénom(s)')
                                        ->placeholder('Ex. : Pape Mamadou')
                                        ->prefixIcon(Heroicon::OutlinedUser)
                                        ->dehydrateStateUsing(fn($state) => $state ? ucwords(mb_strtolower(trim($state))) : null)
                                        ->required()
                                        ->maxLength(100),

                                    TextInput::make('Nom')
                                        ->label('Nom de famille')
                                        ->placeholder('Ex. : NDIAYE')
                                        ->prefixIcon(Heroicon::OutlinedUser)
                                        ->dehydrateStateUsing(fn($state) => $state ? mb_strtoupper(trim($state)) : null)
                                        ->required()
                                        ->maxLength(100),

                                    Select::make('Genre')
                                        ->label('Genre')
                                        ->options([
                                            'Masculin' => 'Masculin',
                                            'Féminin' => 'Féminin',
                                        ])
                                        ->required(),

                                    TextInput::make('Age')
                                        ->label('Âge')
                                        ->numeric()
                                        ->minValue(5)
                                        ->maxValue(120)
                                        ->suffix('ans')
                                        ->placeholder('Ex. : 24')
                                        ->required(),

                                    TextInput::make('Telephone')
                                        ->label('Téléphone personnel')
                                        ->mask('99 999 99 99')
                                        ->placeholder('77 000 00 00')
                                        ->prefixIcon(Heroicon::OutlinedPhone)
                                        ->maxLength(30)
                                        ->required(),

                                    TextInput::make('Adresse')
                                        ->label('Adresse / Quartier')
                                        ->placeholder('Ex. : Quartier Abattoirs')
                                        ->prefixIcon(Heroicon::OutlinedHome)
                                        ->maxLength(255)
                                        ->required(),

                                    Select::make('Niveau_Etude')
                                        ->label('Niveau d\'étude')
                                        ->options([
                                            'Aucun' => 'Aucun',
                                            'Primaire' => 'Primaire',
                                            'Secondaire' => 'Secondaire',
                                            'Baccalauréat' => 'Baccalauréat',
                                            'Licence / Bac+3' => 'Licence / Bac+3',
                                            'Master / Bac+5' => 'Master / Bac+5',
                                            'Doctorat' => 'Doctorat',
                                            'Formation Professionnelle' => 'Formation Professionnelle',
                                            'École Coranique / Arabe' => 'École Coranique / Arabe',
                                        ])
                                        ->searchable()
                                        ->required(),

                                    TextInput::make('Profession')
                                        ->label('Profession / Activité')
                                        ->placeholder('Ex. : Étudiant, Enseignant...')
                                        ->prefixIcon(Heroicon::OutlinedBriefcase)
                                        ->maxLength(150)
                                        ->required(),

                                    TextInput::make('internal_id')
                                        ->label('Matricule attribué')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->prefixIcon(Heroicon::OutlinedIdentification)
                                        ->columnSpanFull()
                                        ->visible(fn(string $operation): bool => $operation === 'edit'),
                                ]),
                        ]),

                    // =========================================================
                    // ÉTAPE 2 : CONSEIL & SECTION (2 COLONNES ÉQUILIBRÉES)
                    // =========================================================
                    Step::make('Conseil & Section')
                        ->description('Structure territoriale de rattachement')
                        ->icon(Heroicon::OutlinedMapPin)
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Select::make('Conseil')
                                        ->label('Conseil d\'appartenance')
                                        ->placeholder('Sélectionnez un conseil')
                                        ->options(fn() => Conseil::query()->pluck('Nom', 'Nom')->toArray())
                                        ->default(function () {
                                            $user = Auth::user();
                                            if ($user && method_exists($user, 'getMaSection') && $section = $user->getMaSection()) {
                                                return $section->conseil?->Nom;
                                            }
                                            return null;
                                        })
                                        ->disabled(function (): bool {
                                            $user = Auth::user();

                                            return $user !== null
                                                && is_callable([$user, 'hasRole'])
                                                && (bool) call_user_func([$user, 'hasRole'], 'point_focal');
                                        })
                                        ->dehydrated()
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->afterStateUpdated(fn(Set $set) => $set('Section', null))
                                        ->required(),

                                    Select::make('Section')
                                        ->label('Section d\'appartenance')
                                        ->placeholder('Sélectionnez une section')
                                        ->options(function (Get $get): array {
                                            $conseilNom = $get('Conseil');
                                            if (! $conseilNom) {
                                                return SectionModel::query()->pluck('Nom', 'Nom')->toArray();
                                            }
                                            $conseil = Conseil::where('Nom', $conseilNom)->first();
                                            return $conseil
                                                ? SectionModel::where('conseil_id', $conseil->id)->pluck('Nom', 'Nom')->toArray()
                                                : [];
                                        })
                                        ->default(function () {
                                            $user = Auth::user();
                                            if ($user && method_exists($user, 'getMaSection') && $section = $user->getMaSection()) {
                                                return $section->Nom;
                                            }
                                            return null;
                                        })
                                        ->disabled(function (): bool {
                                            $user = Auth::user();

                                            return $user !== null
                                                && is_callable([$user, 'hasRole'])
                                                && (bool) call_user_func([$user, 'hasRole'], 'point_focal');
                                        })
                                        ->dehydrated()
                                        ->searchable()
                                        ->preload()
                                        ->required(),
                                ]),
                        ]),

                    // =========================================================
                    // ÉTAPE 3 : INFORMATIONS COMPLÉMENTAIRES (2 COLONNES ÉQUILIBRÉES)
                    // =========================================================
                    Step::make('Informations complémentaires')
                        ->description('Pièce d\'identité, contact d\'urgence et validation')
                        ->icon(Heroicon::OutlinedShieldCheck)
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Select::make('Type_piece_identite')
                                        ->label('Type de pièce d\'identité')
                                        ->options([
                                            'Pas encore de pièce' => 'Pas encore de pièce',
                                            'CNI' => 'Carte Nationale d\'Identité (CNI)',
                                            'Passeport' => 'Passeport',
                                            'Extrait de naissance' => 'Extrait de naissance',
                                            'Carte scolaire / Étudiant' => 'Carte scolaire / Étudiant',
                                            'Autre' => 'Autre',
                                        ])
                                        ->default('Pas encore de pièce')
                                        ->live()
                                        ->afterStateUpdated(function (Set $set, ?string $state): void {
                                            if ($state === 'Pas encore de pièce') {
                                                $set('Numero_piece_identite', 'Pas encore de pièce');
                                            } else {
                                                $set('Numero_piece_identite', null);
                                            }
                                        })
                                        ->dehydrateStateUsing(fn($state) => filled($state) ? $state : 'Pas encore de pièce'),

                                    TextInput::make('Numero_piece_identite')
                                        ->label('Numéro de la pièce')
                                        ->placeholder('Ex. : 1 754 1998 00123')
                                        ->default('Pas encore de pièce')
                                        ->prefixIcon(Heroicon::OutlinedIdentification)
                                        ->maxLength(50)
                                        ->disabled(fn(Get $get): bool => $get('Type_piece_identite') === 'Pas encore de pièce')
                                        ->dehydrated()
                                        ->dehydrateStateUsing(function ($state, Get $get): string {
                                            if ($get('Type_piece_identite') === 'Pas encore de pièce' || blank($state)) {
                                                return 'Pas encore de pièce';
                                            }
                                            return trim((string) $state);
                                        }),

                                    TextInput::make('Parent')
                                        ->label('Parent / Tuteur / Proche')
                                        ->placeholder('Ex. : Fatou DIOP')
                                        ->prefixIcon(Heroicon::OutlinedUser)
                                        ->maxLength(150)
                                        ->required(),

                                    Select::make('Lien_parental')
                                        ->label('Lien de parenté')
                                        ->options([
                                            'Père' => 'Père',
                                            'Mère' => 'Mère',
                                            'Frère / Sœur' => 'Frère / Sœur',
                                            'Conjoint(e)' => 'Conjoint(e)',
                                            'Tuteur' => 'Tuteur',
                                            'Autre' => 'Autre',
                                        ])
                                        ->required(),

                                    TextInput::make('Contact')
                                        ->label('Téléphone du proche')
                                        ->mask('99 999 99 99')
                                        ->placeholder('77 000 00 00')
                                        ->prefixIcon(Heroicon::OutlinedPhone)
                                        ->maxLength(30)
                                        ->required(),

                                    TextInput::make('Cree_par')
                                        ->label('Enrôlé par')
                                        ->default(fn() => Auth::user()?->name)
                                        ->prefixIcon(Heroicon::OutlinedUser)
                                        ->disabled()
                                        ->dehydrated(false),

                                    Toggle::make('Statut')
                                        ->label('Valider et transférer dans la liste des Membres')
                                        ->helperText('Activez pour intégrer immédiatement ce dossier aux membres actifs.')
                                        ->onColor('success')
                                        ->offColor('danger')
                                        ->default(true)
                                        ->inline(false)
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ])
                    ->skippable(false)
                    ->submitAction(new HtmlString('
                    <button type="submit" class="fi-btn fi-btn-size-md fi-color-primary fi-bg-color-600 hover:fi-bg-color-500 text-white font-semibold px-4 py-2 rounded-lg shadow">
                        Enregistrer l\'enrôlement
                    </button>
                '))
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}
