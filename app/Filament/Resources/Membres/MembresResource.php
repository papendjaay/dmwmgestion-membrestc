<?php

namespace App\Filament\Resources\Membres;

use App\Filament\Resources\Membres\Pages\ListMembres;
use App\Filament\Resources\Membres\Schemas\MembresForm;
use App\Filament\Resources\Membres\Tables\MembresTable;
use App\Models\Membres;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MembresResource extends Resource
{
    protected static ?string $model = Membres::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string | \UnitEnum | null $navigationGroup = 'Gestion des Membres';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return MembresForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MembresTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMembres::route('/'),
        ];
    }
}
