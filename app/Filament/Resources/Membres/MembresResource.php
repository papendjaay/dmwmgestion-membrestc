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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MembresResource extends Resource
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (! $user || (method_exists($user, 'hasRole') && $user->hasRole(['Super Admin', 'super_admin']))) {
            return $query;
        }

        // Point Focal de Conseil : voit tous les membres de son Conseil
        if (method_exists($user, 'hasRole') && $user->hasRole('point_focal_conseil')) {
            $monConseil = method_exists($user, 'getMonConseil') ? $user->getMonConseil() : null;

            if ($monConseil && ! empty($monConseil->Nom)) {
                return $query->whereRaw('LOWER(TRIM(Conseil)) = ?', [mb_strtolower(trim($monConseil->Nom))]);
            }

            return $query->whereRaw('1 = 0');
        }

        // Point Focal de Section : voit uniquement les membres de sa Section
        $maSection = method_exists($user, 'getMaSection') ? $user->getMaSection() : null;

        if ($maSection && ! empty($maSection->Nom)) {
            return $query->whereRaw('LOWER(TRIM(Section)) = ?', [mb_strtolower(trim($maSection->Nom))]);
        }

        return $query->whereRaw('1 = 0');
    }
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
