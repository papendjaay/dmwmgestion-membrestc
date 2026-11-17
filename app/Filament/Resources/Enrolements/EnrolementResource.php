<?php

namespace App\Filament\Resources\Enrolements;

use App\Filament\Resources\Enrolements\Pages\CreateEnrolement;
use App\Filament\Resources\Enrolements\Pages\EditEnrolement;
use App\Filament\Resources\Enrolements\Pages\ListEnrolements;
use App\Filament\Resources\Enrolements\Pages\ViewEnrolement;
use App\Filament\Resources\Enrolements\Schemas\EnrolementForm;
use App\Filament\Resources\Enrolements\Schemas\EnrolementInfolist;
use App\Filament\Resources\Enrolements\Tables\EnrolementsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class EnrolementResource extends Resource
{
    protected static string | \UnitEnum | null $navigationGroup = 'Gestion des Membres';
    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Enrôlement';
    protected static ?string $pluralModelLabel = 'Enrôlements';
    protected static ?string $navigationLabel = 'Enrôlements';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static ?string $model = 'App\\Models\\Enrolement';

    protected static ?string $recordTitleAttribute = 'Prenom';

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['super_admin', 'point_focal', 'ca'])) {
            return true;
        }

        return parent::canViewAny();
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['super_admin', 'point_focal', 'ca'])) {
            return true;
        }

        return parent::canCreate();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        // Le super_admin voit tout
        if (is_callable([$user, 'hasRole']) && call_user_func([$user, 'hasRole'], 'super_admin')) {
            return $query;
        }

        // Récupère la section du Point Focal connecté
        $section = method_exists($user, 'getMaSection') ? $user->getMaSection() : null;

        if (! $section) {
            return $query->whereRaw('1 = 0');
        }

        // Filtre uniquement les membres de sa section
        return $query->where('Section', $section->Nom);
    }

    public static function form(Schema $schema): Schema
    {
        return EnrolementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EnrolementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EnrolementsTable::configure($table);
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
            'index' => ListEnrolements::route('/'),
            'create' => CreateEnrolement::route('/create'),
            'view' => ViewEnrolement::route('/{record}'),
            'edit' => EditEnrolement::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
