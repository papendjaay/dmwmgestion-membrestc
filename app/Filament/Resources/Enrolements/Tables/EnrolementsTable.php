<?php

namespace App\Filament\Resources\Enrolements\Tables;

use App\Models\Conseil;
use App\Models\Enrolement;
use App\Models\Section;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EnrolementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->extraAttributes([
                'class' => 'overflow-x-auto [&_table]:min-w-full [&_table]:w-max [&_thead]:bg-[#009CFE] [&_th]:px-2 [&_th]:py-2 [&_th]:whitespace-nowrap [&_th_*]:text-black [&_th_*]:font-bold [&_th_*]:uppercase [&_th_*]:text-xs [&_th]:border-none [&_th:first-child]:w-8 [&_td:first-child]:w-8 [&_td]:border [&_td]:border-gray-200 dark:[&_td]:border-gray-600 [&_td]:text-black dark:[&_td]:text-white [&_td]:font-semibold [&_td]:text-xs [&_td]:uppercase [&_td]:px-2 [&_td]:py-1.5 [&_tr:nth-child(even)]:bg-[#F3F3F3] dark:[&_tr:nth-child(even)]:bg-[#1E293B] [&_tr:hover]:bg-[#E0E0E0] dark:[&_tr:hover]:bg-[#334155]',
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nouvel enrôlement')
                    ->icon(Heroicon::OutlinedPlusCircle)
                    ->color('primary')
                    ->authorize(false)
                    ->visible(function (): bool {
                        $user = Auth::user();
                        return $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['super_admin', 'point_focal']);
                    }),

                Action::make('export_excel')
                    ->label('Exporter Excel')
                    ->icon(Heroicon::OutlinedDocumentArrowDown)
                    ->color('success')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $fileName = 'enrolements_' . now()->format('Y-m-d_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $handle = fopen('php://output', 'w');
                            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

                            fputcsv($handle, [
                                'Matricule',
                                'Prénom(s)',
                                'Nom',
                                'Genre',
                                'Âge',
                                'Niveau d\'étude',
                                'Profession',
                                'Conseil',
                                'Section',
                                'Téléphone',
                                'Adresse',
                                'Type Pièce',
                                'N° Pièce',
                                'Parent / Proche',
                                'Lien Parental',
                                'Contact Proche',
                                'Enrôlé par',
                                'Statut'
                            ], ';');

                            foreach ($records as $row) {
                                fputcsv($handle, [
                                    $row->internal_id,
                                    $row->Prenom,
                                    $row->Nom,
                                    $row->Genre,
                                    $row->Age,
                                    $row->Niveau_Etude,
                                    $row->Profession,
                                    $row->Conseil,
                                    $row->Section,
                                    $row->Telephone,
                                    $row->Adresse,
                                    $row->Type_piece_identite,
                                    $row->Numero_piece_identite,
                                    $row->Parent,
                                    $row->Lien_parental,
                                    $row->Contact,
                                    $row->Cree_par,
                                    $row->Statut ? 'Actif' : 'Inactif',
                                ], ';');
                            }

                            fclose($handle);
                        }, $fileName, [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                        ]);
                    }),

                Action::make('export_pdf')
                    ->label('Exporter PDF')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->color('danger')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $titre = 'Liste des Enrôlements';
                        $fileName = 'enrolements_' . now()->format('Y-m-d_His') . '.pdf';

                        $pdf = Pdf::loadView('exports.liste-membres-pdf', compact('records', 'titre'))
                            ->setPaper('a4', 'landscape');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, $fileName, [
                            'Content-Type' => 'application/pdf',
                        ]);
                    }),
            ])
            ->columns([
                TextColumn::make('index')
                    ->label('N°')
                    ->rowIndex()
                    ->alignment(Alignment::Center)
                    ->color('warning'),

                TextColumn::make('internal_id')
                    ->label('MATRICULE')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->alignment(Alignment::Center),

                TextColumn::make('Prenom')
                    ->label('PRÉNOM(S)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Nom')
                    ->label('NOM')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('Genre')
                    ->label('GENRE')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'Masculin' => 'info',
                        'Féminin' => 'danger',
                        default => 'gray',
                    })
                    ->alignment(Alignment::Center),

                TextColumn::make('Age')
                    ->label('ÂGE')
                    ->suffix(' ans')
                    ->placeholder('-')
                    ->sortable()
                    ->alignment(Alignment::Center),

                TextColumn::make('Niveau_Etude')
                    ->label('NIVEAU D\'ÉTUDE')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('Profession')
                    ->label('PROFESSION')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('Conseil')
                    ->label('CONSEIL')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->sortable()
                    ->alignment(Alignment::Center),

                TextColumn::make('Section')
                    ->label('SECTION')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable()
                    ->alignment(Alignment::Center),

                TextColumn::make('Telephone')
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
                    ->placeholder('Non renseigné')
                    ->searchable()
                    ->alignment(Alignment::Center),

                TextColumn::make('Adresse')
                    ->label('ADRESSE')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('Type_piece_identite')
                    ->label('TYPE PIÈCE')
                    ->badge()
                    ->color(fn(?string $state): string => $state === 'Pas encore de pièce' ? 'warning' : 'gray')
                    ->default('Pas encore de pièce')
                    ->alignment(Alignment::Center),

                TextColumn::make('Numero_piece_identite')
                    ->label('N° PIÈCE')
                    ->badge()
                    ->color(fn(?string $state): string => $state === 'Pas encore de pièce' ? 'warning' : 'info')
                    ->default('Pas encore de pièce')
                    ->searchable()
                    ->alignment(Alignment::Center),

                TextColumn::make('Parent')
                    ->label('PARENT / PROCHE')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('Lien_parental')
                    ->label('LIEN PARENTAL')
                    ->placeholder('-')
                    ->alignment(Alignment::Center),

                TextColumn::make('Contact')
                    ->label('CONTACT PROCHE')
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
                    ->placeholder('Non renseigné')
                    ->searchable()
                    ->alignment(Alignment::Center),

                TextColumn::make('Adresse')
                    ->label('ADRESSE')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('Type_piece_identite')
                    ->label('TYPE PIÈCE')
                    ->badge()
                    ->color(fn(?string $state): string => $state === 'Pas encore de pièce' ? 'warning' : 'gray')
                    ->default('Pas encore de pièce')
                    ->alignment(Alignment::Center),

                TextColumn::make('Numero_piece_identite')
                    ->label('N° PIÈCE')
                    ->badge()
                    ->color(fn(?string $state): string => $state === 'Pas encore de pièce' ? 'warning' : 'info')
                    ->default('Pas encore de pièce')
                    ->searchable()
                    ->alignment(Alignment::Center),

                TextColumn::make('Parent')
                    ->label('PARENT / PROCHE')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('Lien_parental')
                    ->label('LIEN PARENTAL')
                    ->placeholder('-')
                    ->alignment(Alignment::Center),

                TextColumn::make('Contact')
                    ->label('CONTACT PROCHE')
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
                    ->placeholder('Non renseigné')
                    ->searchable()
                    ->alignment(Alignment::Center),

                TextColumn::make('Cree_par')
                    ->label('ENRÔLÉ PAR')
                    ->placeholder('-')
                    ->searchable(),

                IconColumn::make('Statut')
                    ->label('STATUT')
                    ->state(fn($record): bool => in_array(strtolower(trim((string) $record->Statut)), ['actif', 'activé', 'active', '1', 'true', 'oui'], true))
                    ->boolean()
                    ->alignment(Alignment::Center),

                TextColumn::make('created_at')
                    ->label('DATE D\'ENRÔLEMENT')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->alignment(Alignment::Center),
            ])
            ->filters([
                // --- FILTRES RÉSERVÉS UNIQUEMENT AU SUPER ADMIN ---
                SelectFilter::make('Conseil')
                    ->label('Filtrer par Conseil')
                    ->options(fn() => Conseil::query()->pluck('Nom', 'Nom')->toArray())
                    ->searchable()
                    ->visible(function (): bool {
                        $user = Auth::user();
                        return $user && method_exists($user, 'hasRole') && $user->hasRole('super_admin');
                    }),

                SelectFilter::make('Section')
                    ->label('Filtrer par Section')
                    ->options(fn() => Section::query()->pluck('Nom', 'Nom')->toArray())
                    ->searchable()
                    ->visible(function (): bool {
                        $user = Auth::user();
                        return $user && method_exists($user, 'hasRole') && $user->hasRole('super_admin');
                    }),

                // --- FILTRES DISPONIBLES POUR TOUS LES RÔLES ---
                SelectFilter::make('Genre')
                    ->label('Filtrer par Genre')
                    ->options([
                        'Masculin' => 'Masculin',
                        'Féminin' => 'Féminin',
                    ]),

                SelectFilter::make('tranche_age')
                    ->label('Filtrer par Tranche d\'âge')
                    ->options([
                        'moins_18' => 'Moins de 18 ans',
                        '18_35' => '18 à 35 ans (Jeunes)',
                        'plus_35' => 'Plus de 35 ans',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'moins_18' => $query->where('Age', '<', 18),
                            '18_35' => $query->whereBetween('Age', [18, 35]),
                            'plus_35' => $query->where('Age', '>', 35),
                            default => $query,
                        };
                    }),

                SelectFilter::make('Niveau_Etude')
                    ->label('Filtrer par Niveau d\'étude')
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
                    ->searchable(),

                SelectFilter::make('Profession')
                    ->label('Filtrer par Profession')
                    ->options(
                        fn(): array => Enrolement::query()
                            ->whereNotNull('Profession')
                            ->where('Profession', '!=', '')
                            ->distinct()
                            ->pluck('Profession', 'Profession')
                            ->toArray()
                    )
                    ->searchable(),

                SelectFilter::make('Type_piece_identite')
                    ->label('Filtrer par Pièce d\'identité')
                    ->options([
                        'Pas encore de pièce' => 'Pas encore de pièce',
                        'CNI' => 'Carte Nationale d\'Identité (CNI)',
                        'Passeport' => 'Passeport',
                        'Extrait de naissance' => 'Extrait de naissance',
                        'Carte scolaire / Étudiant' => 'Carte scolaire / Étudiant',
                        'Autre' => 'Autre',
                    ]),

                SelectFilter::make('Lien_parental')
                    ->label('Filtrer par Lien parental')
                    ->options([
                        'Père' => 'Père',
                        'Mère' => 'Mère',
                        'Frère / Sœur' => 'Frère / Sœur',
                        'Conjoint(e)' => 'Conjoint(e)',
                        'Tuteur' => 'Tuteur',
                        'Autre' => 'Autre',
                    ]),

                SelectFilter::make('Statut')
                    ->label('Filtrer par Statut')
                    ->options([
                        '1' => 'Validé (Actif)',
                        '0' => 'Non validé (Inactif)',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
