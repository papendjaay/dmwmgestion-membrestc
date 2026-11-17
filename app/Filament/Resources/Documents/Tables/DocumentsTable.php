<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Models\Document;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('titre')
                    ->label('TITRE')
                    ->description(fn(Document $record): ?string => $record->description)
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('user.name')
                    ->label('PUBLIÉ PAR')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('DATE DE PUBLICATION')
                    ->dateTime('d/m/Y à H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('lire')
                    ->label('Lire')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->url(fn(Document $record): string => Storage::url($record->fichier), shouldOpenInNewTab: true),

                Action::make('telecharger')
                    ->label('Télécharger')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(fn(Document $record) => response()->download(
                        Storage::disk('public')->path($record->fichier),
                        str($record->titre)->slug() . '.pdf',
                    )),

                EditAction::make()
                    ->visible(fn(Document $record): bool => $record->user_id === Auth::id()
                        || (is_callable([Auth::user(), 'hasRole']) && call_user_func([Auth::user(), 'hasRole'], 'super_admin'))),

                DeleteAction::make()
                    ->visible(fn(Document $record): bool => $record->user_id === Auth::id()
                        || (is_callable([Auth::user(), 'hasRole']) && call_user_func([Auth::user(), 'hasRole'], 'super_admin'))),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
