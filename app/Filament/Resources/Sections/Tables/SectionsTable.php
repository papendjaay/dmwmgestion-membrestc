<?php

namespace App\Filament\Resources\Sections\Tables;

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

class SectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->extraAttributes([
                'class' => 'overflow-x-auto [&_table]:min-w-full [&_table]:w-max [&_thead]:bg-[#009CFE] [&_th]:px-2 [&_th]:py-2 [&_th]:whitespace-nowrap [&_th_*]:text-black [&_th_*]:font-bold [&_th_*]:uppercase [&_th_*]:text-xs [&_th]:border-none [&_th:first-child]:w-8 [&_td:first-child]:w-8 [&_td]:border [&_td]:border-gray-200 dark:[&_td]:border-gray-600 [&_td]:text-black dark:[&_td]:text-white [&_td]:font-semibold [&_td]:text-xs [&_td]:uppercase [&_td]:px-2 [&_td]:py-1.5 [&_tr:nth-child(even)]:bg-[#F3F3F3] dark:[&_tr:nth-child(even)]:bg-[#1E293B] [&_tr:hover]:bg-[#E0E0E0] dark:[&_tr:hover]:bg-[#334155]',
            ])
            ->columns([
                TextColumn::make('index')
                    ->label('N°')
                    ->rowIndex()
                    ->alignment(Alignment::Center)
                    ->color('warning')
                    ->extraAttributes(['class' => 'whitespace-nowrap']),

                TextColumn::make('conseil.Nom')
                    ->label('CONSEIL')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary')
                    ->alignment(Alignment::Center)
                    ->extraAttributes(['class' => 'whitespace-nowrap']),

                TextColumn::make('Nom')
                    ->label('SECTION')
                    ->searchable()
                    ->sortable()
                    ->color('warning')
                    ->alignment(Alignment::Center)
                    ->badge()
                    ->extraAttributes(['class' => 'whitespace-nowrap']),

                // --- POINT FOCAL 1 & TEL ---
                TextColumn::make('PF1')
                    ->label('PF1')
                    ->color('success')
                    ->placeholder('Non renseigné')
                    ->alignment(Alignment::Center)
                    ->extraAttributes(['class' => 'whitespace-nowrap'])
                    ->searchable(),

                TextColumn::make('tel_pf1')
                    ->label('TEL-PF1')
                    ->state(fn($record) => $record->tel_pf1 ?? $record->TEL_PF1)
                    ->url(function ($state): ?string {
                        if (blank($state)) {
                            return null;
                        }
                        $numero = preg_replace('/\s+/', '', $state);
                        return "tel:+221{$numero}";
                    })
                    ->icon(Heroicon::OutlinedPhone)
                    ->badge()
                    ->placeholder('Non renseigné')
                    ->alignment(Alignment::Center)
                    ->extraAttributes(['class' => 'whitespace-nowrap'])
                    ->color('info')
                    ->searchable(),

                // --- POINT FOCAL 2 & TEL ---
                TextColumn::make('PF2')
                    ->label('PF2')
                    ->color('success')
                    ->placeholder('Non renseigné')
                    ->alignment(Alignment::Center)
                    ->extraAttributes(['class' => 'whitespace-nowrap'])
                    ->searchable(),

                TextColumn::make('tel_pf2')
                    ->label('TEL-PF2')
                    ->state(fn($record) => $record->tel_pf2 ?? $record->TEL_PF2)
                    ->url(function ($state): ?string {
                        if (blank($state)) {
                            return null;
                        }
                        $numero = preg_replace('/\s+/', '', $state);
                        return "tel:+221{$numero}";
                    })
                    ->icon(Heroicon::OutlinedPhone)
                    ->badge()
                    ->placeholder('Non renseigné')
                    ->alignment(Alignment::Center)
                    ->extraAttributes(['class' => 'whitespace-nowrap'])
                    ->color('info')
                    ->searchable(),

                // --- POINT FOCAL 3 & TEL ---
                TextColumn::make('PF3')
                    ->label('PF3')
                    ->color('success')
                    ->placeholder('Non renseigné')
                    ->alignment(Alignment::Center)
                    ->extraAttributes(['class' => 'whitespace-nowrap'])
                    ->searchable(),

                TextColumn::make('tel_pf3')
                    ->label('TEL-PF3')
                    ->state(fn($record) => $record->tel_pf3 ?? $record->TEL_PF3)
                    ->url(function ($state): ?string {
                        if (blank($state)) {
                            return null;
                        }
                        $numero = preg_replace('/\s+/', '', $state);
                        return "tel:+221{$numero}";
                    })
                    ->icon(Heroicon::OutlinedPhone)
                    ->badge()
                    ->placeholder('Non renseigné')
                    ->alignment(Alignment::Center)
                    ->extraAttributes(['class' => 'whitespace-nowrap'])
                    ->color('info')
                    ->searchable(),

                // --- STATUT ---
                IconColumn::make('status')
                    ->label('STATUT')
                    ->state(fn($record): bool => in_array($record->status, ['Active', '1', 1, true], true))
                    ->boolean()
                    ->alignment(Alignment::Center),
            ])
            ->filters([
                SelectFilter::make('conseil_id')
                    ->label('Filtrer par Conseil')
                    ->relationship('conseil', 'Nom')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Statut de validation')
                    ->options([
                        'Active' => 'Validée (Active)',
                        'Inactive' => 'Non validée (Inactive)',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->modalWidth('2xl'),
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
