<?php

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Journal d\'audit';
    protected static ?string $modelLabel = 'Activité';
    protected static ?string $pluralModelLabel = 'Journal d\'audit';
    protected static string|\UnitEnum|null $navigationGroup = 'Sécurité & Accès'; // Aligné avec Users & Rôles
    protected static ?int $navigationSort = 99;

    // Seuls les Super Admins peuvent voir ce menu et accéder à la page.

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->hasRole(['super_admin', 'Super Admin']) ?? false;
    }


    // Interdire toute création, modification ou suppression des logs (intégrité)
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Heure')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Utilisateur')
                    ->default('Système / Tentative externe')
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Action')
                    ->badge()
                    ->color(fn(string $state): string => match (true) {
                        str_contains($state, 'Échec') => 'danger',
                        str_contains($state, 'deleted') => 'warning',
                        str_contains($state, 'Connexion') => 'success',
                        default => 'info',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Cible')
                    ->formatStateUsing(fn($state) => $state ? class_basename($state) : '-')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('properties.ip')
                    ->label('Adresse IP')
                    ->default('-'),

                Tables\Columns\TextColumn::make('properties.email')
                    ->label('Email testé (si échec)')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->label('Type d\'événement')
                    ->options([
                        'default' => 'Modifications de comptes',
                        'auth' => 'Connexions & Tentatives',
                    ]),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('supprimer_selection')
                        ->label('Supprimer la sélection')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->visible(function (): bool {
                            $user = Auth::user();

                            return $user instanceof \App\Models\User
                                && $user->hasRole(['super_admin', 'Super Admin']);
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Supprimer les entrées sélectionnées ?')
                        ->modalDescription('Les lignes cochées seront définitivement supprimées du journal d\'audit.')
                        ->modalSubmitActionLabel('Oui, supprimer')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            $nombre = $records->count();
                            $records->each->delete();

                            // Trace la suppression sélective pour garder l'intégrité de l'audit
                            activity('auth')
                                ->causedBy(Auth::user())
                                ->withProperties([
                                    'ip' => request()->ip(),
                                    'lignes_supprimees' => $nombre,
                                ])
                                ->log("Suppression sélective de {$nombre} entrée(s) dans le journal d'audit");

                            Notification::make()
                                ->title("{$nombre} entrée(s) supprimée(s)")
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
        ];
    }
}
