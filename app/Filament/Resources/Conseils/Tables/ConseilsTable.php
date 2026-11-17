<?php

namespace App\Filament\Resources\Conseils\Tables;

use App\Models\Conseil;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConseilsTable
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

                TextColumn::make('Nom')
                    ->label('NOM')
                    ->searchable()
                    ->sortable()
                    ->color('warning')
                    ->alignment(Alignment::Center)
                    ->badge()
                    ->extraAttributes(['class' => 'whitespace-nowrap']),

                TextColumn::make('Code')
                    ->label('CODE')
                    ->color('warning')
                    ->alignment(Alignment::Center)
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['class' => 'whitespace-nowrap']),

                TextColumn::make('sections_count')
                    ->label('SECTIONS')
                    ->counts('sections')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->alignment(Alignment::Center)
                    ->extraAttributes(['class' => 'whitespace-nowrap']),

                // --- POINT FOCAL 1 & TEL ---
                TextColumn::make('PF1')
                    ->label('PF1')
                    ->color('success')
                    ->alignment(Alignment::Center)
                    ->extraAttributes(['class' => 'whitespace-nowrap'])
                    ->searchable(),

                TextColumn::make('tel_pf1')
                    ->label('TEL-PF1')
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
                    ->color('info'),

                // --- POINT FOCAL 2 & TEL ---
                TextColumn::make('PF2')
                    ->label('PF2')
                    ->color('success')
                    ->alignment(Alignment::Center)
                    ->extraAttributes(['class' => 'whitespace-nowrap'])
                    ->searchable(),

                TextColumn::make('tel_pf2')
                    ->label('TEL-PF2')
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
                    ->color('info'),

                // --- POINT FOCAL 3 & TEL ---
                TextColumn::make('PF3')
                    ->label('PF3')
                    ->color('success')
                    ->alignment(Alignment::Center)
                    ->extraAttributes(['class' => 'whitespace-nowrap'])
                    ->searchable(),

                TextColumn::make('tel_pf3')
                    ->label('TEL-PF3')
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
                    ->color('info'),
            ])
            ->filters([
                SelectFilter::make('Nom')
                    ->label('Filtrer par Nom')
                    ->options(
                        fn(): array => Conseil::query()
                            ->whereNotNull('Nom')
                            ->distinct()
                            ->pluck('Nom', 'Nom')
                            ->toArray()
                    )
                    ->searchable(),

                SelectFilter::make('Code')
                    ->label('Filtrer par Code')
                    ->options(
                        fn(): array => Conseil::query()
                            ->whereNotNull('Code')
                            ->distinct()
                            ->pluck('Code', 'Code')
                            ->toArray()
                    )
                    ->searchable(),

                Filter::make('has_pf')
                    ->label('Avec Point Focal assigné')
                    ->query(
                        fn(Builder $query): Builder => $query->where(
                            fn(Builder $subQuery): Builder => $subQuery
                                ->whereNotNull('PF1')
                                ->orWhereNotNull('PF2')
                                ->orWhereNotNull('PF3')
                        )
                    ),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Voir'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
