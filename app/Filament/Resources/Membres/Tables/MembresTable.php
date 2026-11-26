<?php

namespace App\Filament\Resources\Membres\Tables;

use App\Models\Conseil;
use App\Models\Section;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
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
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MembresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->extraAttributes([
                'class' => 'overflow-x-auto [&_table]:min-w-full [&_table]:w-max [&_thead]:bg-[#009CFE] [&_th]:px-2 [&_th]:py-2 [&_th]:whitespace-nowrap [&_th_*]:text-black [&_th_*]:font-bold [&_th_*]:uppercase [&_th_*]:text-xs [&_th]:border-none [&_th:first-child]:w-8 [&_td:first-child]:w-8 [&_td]:border [&_td]:border-gray-200 dark:[&_td]:border-gray-600 [&_td]:text-black dark:[&_td]:text-white [&_td]:font-semibold [&_td]:text-xs [&_td]:uppercase [&_td]:px-2 [&_td]:py-1.5 [&_tr:nth-child(even)]:bg-[#F3F3F3] dark:[&_tr:nth-child(even)]:bg-[#1E293B] [&_tr:hover]:bg-[#E0E0E0] dark:[&_tr:hover]:bg-[#334155]',
            ])
            ->headerActions([
                Action::make('export_excel')
                    ->label('Exporter Excel')
                    ->icon(Heroicon::OutlinedDocumentArrowDown)
                    ->color('success')
                    ->action(function ($livewire): StreamedResponse {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $fileName = 'membres_' . now()->format('Y-m-d_His') . '.csv';

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
                        $titre = 'Liste Officielle des Membres';
                        $fileName = 'membres_' . now()->format('Y-m-d_His') . '.pdf';

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
            ])
            ->filters([
                SelectFilter::make('Conseil')
                    ->label('Conseil')
                    ->options(fn(): array => Conseil::query()->pluck('Nom', 'Nom')->toArray())
                    ->searchable()
                    ->preload(),

                SelectFilter::make('Section')
                    ->label('Section')
                    ->options(fn(): array => Section::query()->pluck('Nom', 'Nom')->toArray())
                    ->searchable()
                    ->preload(),

                SelectFilter::make('Genre')
                    ->label('Genre')
                    ->options([
                        'Masculin' => 'Masculin',
                        'Féminin'  => 'Féminin',
                    ]),

                SelectFilter::make('Statut')
                    ->label('Statut')
                    ->options([
                        'Enrôlé'  => 'Enrôlé',
                        'Actif'   => 'Actif',
                        'Inactif' => 'Inactif',
                    ]),
            ])

            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('carte_membre')
                        ->label('Carte de Membre (PDF)')
                        ->icon(Heroicon::OutlinedIdentification)
                        ->color('primary')
                        ->action(function ($record): StreamedResponse {
                            $fileName = 'Carte_Membre_' . ($record->internal_id ?? $record->id) . '.pdf';

                            $pdf = Pdf::loadView('exports.carte-membre-pdf', [
                                'membre' => $record,
                            ])->setPaper([0, 0, 242.65, 153.07]);

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, $fileName, [
                                'Content-Type' => 'application/pdf',
                            ]);
                        }),
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
