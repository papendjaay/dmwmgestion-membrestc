<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Partager un document PDF')
                    ->description('Importez un fichier PDF pour le rendre accessible et téléchargeable par tous les utilisateurs.')
                    ->icon(Heroicon::OutlinedDocumentArrowUp)
                    ->schema([
                        TextInput::make('titre')
                            ->label('Titre du document')
                            ->placeholder('Ex: Compte rendu de réunion...')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Description (optionnelle)')
                            ->placeholder('Ajoutez quelques détails sur le contenu de ce document...')
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::make('fichier')
                            ->label('Fichier PDF')
                            ->disk('public')
                            ->directory('documents-pdf')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240) // 10 Mo max
                            ->previewable(false)
                            ->downloadable()
                            ->openable()
                            ->required()
                            ->columnSpanFull(),

                    ]),
            ])
            ->columns(1);
    }
}
