<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Models\Document;
use App\Models\User;
use App\Notifications\DocumentPartageNotification;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        $totalUtilisateurs = User::count();

        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['user', 'lecteurs']))
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->columns([
                // 1. Titre + description
                TextColumn::make('titre')
                    ->label('DOCUMENT')
                    ->icon('heroicon-s-document-text')
                    ->iconColor('danger')
                    ->weight(FontWeight::Bold)
                    ->description(fn(Document $record): ?string => $record->description ? Str::limit($record->description, 80) : 'Aucune description')
                    ->tooltip(fn(Document $record): ?string => $record->description)
                    ->searchable(['titre', 'description'])
                    ->sortable()
                    ->wrap(),

                // 2. Statut personnel pour l'utilisateur connecté
                TextColumn::make('statut_personnel')
                    ->label('MON STATUT')
                    ->badge()
                    ->getStateUsing(fn(Document $record): string => $record->lecteurs->contains('id', Auth::id()) ? 'Lu' : 'Nouveau')
                    ->icon(fn(string $state): string => $state === 'Lu' ? 'heroicon-m-check-circle' : 'heroicon-m-sparkles')
                    ->color(fn(string $state): string => $state === 'Lu' ? 'success' : 'warning'),

                // 3. Compteur global de lecture (réservé au super_admin)
                TextColumn::make('lecteurs_count')
                    ->label('SUIVI LECTURE')
                    ->badge()
                    ->icon('heroicon-m-users')
                    ->getStateUsing(fn(Document $record): string => $record->lecteurs->count() . ' / ' . $totalUtilisateurs)
                    ->color(function (Document $record) use ($totalUtilisateurs): string {
                        $nb = $record->lecteurs->count();
                        if ($totalUtilisateurs > 0 && $nb >= $totalUtilisateurs) {
                            return 'success';
                        }

                        return $nb > 0 ? 'info' : 'gray';
                    })
                    ->visible(function (): bool {
                        $user = Auth::user();

                        return $user instanceof User && $user->hasRole('super_admin');
                    }),

                // 4. Taille du fichier
                TextColumn::make('fichier')
                    ->label('TAILLE')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(function (?string $state): string {
                        if (! $state || ! Storage::disk('public')->exists($state)) {
                            return 'PDF';
                        }

                        return Number::fileSize(Storage::disk('public')->size($state), precision: 1);
                    }),

                // 5. Auteur
                TextColumn::make('user.name')
                    ->label('PUBLIÉ PAR')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                // 6. Date de publication
                TextColumn::make('created_at')
                    ->label('PUBLICATION')
                    ->since()
                    ->description(fn(Document $record): string => $record->created_at?->format('d/m/Y H:i') ?? '')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Filtrer par auteur')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                // Bouton "Lire" : enregistre la lecture et ouvre l'aperçu PDF intégré
                Action::make('lire')
                    ->label('Lire')
                    ->icon('heroicon-m-eye')
                    ->color('success')
                    ->button()
                    ->outlined()
                    ->size('xs')
                    ->mountUsing(fn(Document $record) => $record->marquerCommeLu())
                    ->modalHeading(fn(Document $record): string => $record->titre)
                    ->modalDescription(fn(Document $record): ?string => $record->description)
                    ->modalIcon('heroicon-o-document-text')
                    ->modalIconColor('danger')
                    ->modalWidth(Width::FiveExtraLarge)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer')
                    ->extraModalFooterActions(fn(Document $record): array => [
                        Action::make('telechargerDepuisModal')
                            ->label('Télécharger le PDF')
                            ->icon('heroicon-m-arrow-down-tray')
                            ->color('primary')
                            ->action(function () use ($record) {
                                $record->marquerCommeLu();
                                $path = Storage::disk('public')->path($record->fichier);

                                return file_exists($path) ? response()->download($path, Str::slug($record->titre) . '.pdf') : null;
                            }),
                    ])
                    ->modalContent(function (Document $record): HtmlString {
                        $url = Storage::url($record->fichier);

                        return new HtmlString('
                            <div style="width: 100%; height: 75vh; border-radius: 10px; overflow: hidden; border: 1px solid rgba(148, 163, 184, 0.25); background: #f8fafc;">
                                <iframe
                                    src="' . e($url) . '#toolbar=1&navpanes=0"
                                    style="width: 100%; height: 100%; border: none;"
                                    title="' . e($record->titre) . '"
                                ></iframe>
                            </div>
                        ');
                    }),

                // Bouton "Télécharger" : enregistre aussi la lecture
                Action::make('telecharger')
                    ->label('Télécharger')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('primary')
                    ->button()
                    ->size('xs')
                    ->action(function (Document $record) {
                        $record->marquerCommeLu();
                        $path = Storage::disk('public')->path($record->fichier);
                        if (! file_exists($path)) {
                            return null;
                        }

                        return response()->download($path, Str::slug($record->titre) . '.pdf');
                    }),

                // Bouton "Suivi de lecture" propre et structuré (réservé au super_admin)
                Action::make('suiviLecteurs')
                    ->icon('heroicon-m-clipboard-document-list')
                    ->color('info')
                    ->iconButton()
                    ->tooltip('Voir le détail des lectures')
                    ->modalHeading(fn(Document $record): string => "Suivi de lecture : {$record->titre}")
                    ->modalDescription(function (Document $record): string {
                        $total = User::count();
                        $lus = $record->lecteurs()->count();
                        $pourcentage = $total > 0 ? round(($lus / $total) * 100) : 0;

                        return "Taux de consultation : {$lus} sur {$total} membres ({$pourcentage}%)";
                    })
                    ->modalIcon('heroicon-o-eye')
                    ->modalIconColor('info')
                    ->modalWidth(Width::ThreeExtraLarge)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer')
                    ->extraModalFooterActions(fn(Document $record): array => [
                        Action::make('relancerNonLus')
                            ->label('Relancer les membres en attente')
                            ->icon('heroicon-m-envelope')
                            ->color('warning')
                            ->requiresConfirmation()
                            ->modalHeading('Relancer par e-mail')
                            ->modalDescription('Voulez-vous renvoyer ce document par e-mail uniquement aux membres qui ne l\'ont pas encore consulté ?')
                            ->visible(function () use ($record): bool {
                                $lusIds = $record->lecteurs()->pluck('users.id')->toArray();

                                return User::whereNotIn('id', $lusIds)->whereNotNull('email')->exists();
                            })
                            ->action(function () use ($record) {
                                $lusIds = $record->lecteurs()->pluck('users.id')->toArray();
                                $nonLus = User::whereNotIn('id', $lusIds)
                                    ->whereNotNull('email')
                                    ->where('email', '!=', '')
                                    ->get();
                                if ($nonLus->isNotEmpty()) {
                                    NotificationFacade::send($nonLus, new DocumentPartageNotification($record));
                                    Notification::make()
                                        ->title('Relance envoyée')
                                        ->body("L'e-mail a été mis en file d'attente pour {$nonLus->count()} membre(s).")
                                        ->success()
                                        ->send();
                                }
                            }),
                    ])
                    ->schema(function (Document $record): array {
                        $record->load('lecteurs');
                        $lusIds = $record->lecteurs->pluck('id')->toArray();
                        $nonLus = User::whereNotIn('id', $lusIds)->orderBy('name')->get();
                        $listeLus = $record->lecteurs->map(function ($lecteur) {
                            $dateLu = Carbon::parse($lecteur->pivot->lu_le)->format('d/m/Y à H:i');
                            return "✓ {$lecteur->name} — ({$dateLu})";
                        })->toArray();
                        $listeNonLus = $nonLus->map(fn($user) => "• {$user->name}")->toArray();
                        return [
                            Grid::make([
                                'default' => 1,
                                'md' => 2,
                            ])->schema([
                                Section::make("Ont consulté ({$record->lecteurs->count()})")
                                    ->icon('heroicon-m-check-circle')
                                    ->iconColor('success')
                                    ->compact()
                                    ->schema([
                                        TextEntry::make('lecteurs_list')
                                            ->hiddenLabel()
                                            ->state($listeLus ?: ['Aucune lecture enregistrée pour le moment.'])
                                            ->listWithLineBreaks()
                                            ->badge(fn() => ! empty($listeLus))
                                            ->color(fn() => ! empty($listeLus) ? 'success' : 'gray'),
                                    ]),
                                Section::make("En attente ({$nonLus->count()})")
                                    ->icon('heroicon-m-clock')
                                    ->iconColor('warning')
                                    ->compact()
                                    ->schema([
                                        TextEntry::make('non_lus_list')
                                            ->hiddenLabel()
                                            ->state($listeNonLus ?: ['Tous les membres ont lu ce document !'])
                                            ->listWithLineBreaks()
                                            ->badge(fn() => ! empty($listeNonLus))
                                            ->color(fn() => ! empty($listeNonLus) ? 'warning' : 'success'),
                                    ]),
                            ]),
                        ];
                    })
                    ->visible(function (): bool {
                        $user = Auth::user();
                        return $user instanceof User && $user->hasRole('super_admin');
                    }),
                // Actions réservées uniquement au super_admin
                EditAction::make()
                    ->iconButton()
                    ->tooltip('Modifier le document')
                    ->visible(function (): bool {
                        $user = Auth::user();
                        return $user instanceof User && $user->hasRole('super_admin');
                    }),
                DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Supprimer le document')
                    ->visible(function (): bool {
                        $user = Auth::user();
                        return $user instanceof User && $user->hasRole('super_admin');
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(function (): bool {
                            $user = Auth::user();
                            return $user instanceof User && $user->hasRole('super_admin');
                        }),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-folder-open')
            ->emptyStateHeading('Aucun document partagé pour le moment')
            ->emptyStateDescription('Soyez le premier à partager un compte rendu, un rapport ou un fichier PDF avec les membres.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Partager un document')
                    ->icon('heroicon-m-plus'),
            ]);
    }
}
