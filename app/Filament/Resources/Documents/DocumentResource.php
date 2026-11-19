<?php

namespace App\Filament\Resources\Documents;

use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Filament\Resources\Documents\Schemas\DocumentForm;
use App\Filament\Resources\Documents\Tables\DocumentsTable;
use App\Models\Document;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'titre';

    // 1. Tout utilisateur connecté voit le menu "Documents" et la liste
    public static function canViewAny(): bool
    {
        return Auth::check();
    }

    // 2. Tout utilisateur connecté peut partager (créer) un document
    public static function canCreate(): bool
    {
        return Auth::check();
    }

    // 3. Tout utilisateur connecté peut consulter/télécharger un document partagé
    public static function canView(Model $record): bool
    {
        return Auth::check();
    }

    // 4. Seul le super_admin peut modifier un document
    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();
        return $user instanceof User && $user->hasRole('super_admin');
    }

    // 5. Seul le super_admin peut supprimer un document
    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();
        return $user instanceof User && $user->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return DocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }
}
