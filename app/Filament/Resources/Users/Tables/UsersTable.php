<?php

namespace App\Filament\Resources\Users\Tables;

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
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->extraAttributes([
                'class' => 'overflow-x-auto [&_table]:min-w-full [&_table]:w-max [&_thead]:bg-[#009CFE] [&_th]:px-2 [&_th]:py-2 [&_th]:whitespace-nowrap [&_th_*]:text-black [&_th_*]:font-bold [&_th_*]:uppercase [&_th_*]:text-xs [&_th]:border-none [&_th:first-child]:w-8 [&_td:first-child]:w-8 [&_td]:border [&_td]:border-gray-200 dark:[&_td]:border-gray-600 [&_td]:text-black dark:[&_td]:text-white [&_td]:font-semibold [&_td]:text-xs [&_td]:px-2 [&_td]:py-1.5 [&_tr:nth-child(even)]:bg-[#F3F3F3] dark:[&_tr:nth-child(even)]:bg-[#1E293B] [&_tr:hover]:bg-[#E0E0E0] dark:[&_tr:hover]:bg-[#334155]',
            ])
            ->columns([
                TextColumn::make('index')
                    ->label('N°')
                    ->rowIndex()
                    ->alignment(Alignment::Center)
                    ->color('warning')
                    ->extraAttributes(['class' => 'whitespace-nowrap']),

                TextColumn::make('name')
                    ->label('NOM')
                    ->placeholder('Nom')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes(['class' => 'whitespace-nowrap']),

                TextColumn::make('email')
                    ->label('EMAIL')
                    ->placeholder('Adresse Mail')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->copyable()
                    ->copyMessage('Adresse e-mail copiée !')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes(['class' => 'whitespace-nowrap lowercase']),

                TextColumn::make('role')
                    ->label('RÔLE')
                    ->placeholder('Rôle Assigné')
                    ->getStateUsing(fn($record) => $record->getRoleNames()->first() ?? 'User')
                    ->badge()
                    ->alignment(Alignment::Center)
                    ->color(fn($state) => match (strtolower(str_replace(['-', '_'], ' ', (string) $state))) {
                        'super admin'      => 'warning',
                        'point focal'      => 'info',
                        'pf conseil'       => 'success',
                        'pf section'       => 'primary',
                        'admin', 'admin 1' => 'danger',
                        default            => 'gray',
                    })
                    ->formatStateUsing(fn($state) => str($state)->replace(['_', '-'], ' ')->title())
                    ->extraAttributes(['class' => 'whitespace-nowrap'])
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('rattachement')
                    ->label('CONSEIL / SECTION')
                    ->getStateUsing(function ($record) {
                        $section = $record->getMaSection();

                        if (! $section) {
                            return 'Tous / Global';
                        }

                        // Cherche le premier champ texte pertinent (hors dates et PF1/PF2/PF3)
                        $extractLabel = function ($model) {
                            if (! $model) {
                                return null;
                            }
                            $attrs = collect($model->getAttributes())->except([
                                'id',
                                'conseil_id',
                                'user_id',
                                'PF1',
                                'PF2',
                                'PF3',
                                'pf1',
                                'pf2',
                                'pf3',
                                'created_at',
                                'updated_at',
                            ]);

                            return $attrs->first(fn($val) => is_string($val) && trim($val) !== '');
                        };

                        $nomConseil = $extractLabel($section->conseil);
                        $nomSection = $extractLabel($section);

                        if ($nomConseil && $nomSection) {
                            return "{$nomConseil} — {$nomSection}";
                        }

                        return $nomSection ?: ($nomConseil ?: 'Tous / Global');
                    })
                    ->badge()
                    ->alignment(Alignment::Center)
                    ->color(fn($state) => $state === 'Tous / Global' ? 'gray' : 'warning')
                    ->extraAttributes(['class' => 'whitespace-nowrap'])
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('has_email_authentication')
                    ->label('2FA')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->alignment(Alignment::Center)
                    ->tooltip(fn($state) => $state ? 'Double authentification activée' : 'Double authentification désactivée'),

                TextColumn::make('created_at')
                    ->label('CRÉÉ LE')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('MODIFIÉ LE')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Filtrer par Rôle')
                    ->relationship('roles', 'name')
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->modalWidth('2xl'),
                    EditAction::make()
                        ->hidden(function ($record): bool {
                            /** @var \App\Models\User|null $user */
                            $user = Auth::user();

                            return $record->getRoleNames()->contains('super_admin')
                                && ! ($user?->getRoleNames()->contains('super_admin') ?? false);
                        }),
                    DeleteAction::make()
                        ->hidden(fn($record) => $record->getRoleNames()->contains('super_admin')),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, $records) {
                            if ($records->contains(fn($record) => $record->getRoleNames()->contains('super_admin'))) {
                                $action->cancel();
                            }
                        }),
                ]),
            ]);
    }
}
