<?php

namespace App\Filament\Exports;

use App\Models\Membre;
use App\Models\Membres;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class MembresExporter extends Exporter
{
    protected static ?string $model = Membres::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ORDRE'),

            ExportColumn::make('internal_id')
                ->label('MATRICULE'),

            ExportColumn::make('Prenom')
                ->label('PRENOM'),

            ExportColumn::make('Nom')
                ->label('NOM'),

            ExportColumn::make('Genre')
                ->label('GENRE'),

            ExportColumn::make('Age')
                ->label('AGE'),

            ExportColumn::make('Niveau_Etude')
                ->label('NIVEAU ETUDE'),

            ExportColumn::make('Profession')
                ->label('PROFESSION'),

            ExportColumn::make('Conseil')
                ->label('CONSEIL'),

            ExportColumn::make('Section')
                ->label('SECTION'),

            ExportColumn::make('Telephone')
                ->label('TELEPHONE'),

            ExportColumn::make('Adresse')
                ->label('ADRESSE'),

            ExportColumn::make('Type_piece_identite')
                ->label('TYPE PIECE'),

            ExportColumn::make('Numero_piece_identite')
                ->label('NUMERO PIECE'),

            ExportColumn::make('Parent')
                ->label('PARENT'),

            ExportColumn::make('Lien_parental')
                ->label('LIEN PARENTAL'),

            ExportColumn::make('Contact')
                ->label('CONTACT'),

            ExportColumn::make('Cree_par')
                ->label('CREER PAR'),

            ExportColumn::make('Statut')
                ->label('STATUT'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'L\'exportation de vos membres est terminée et ' . Number::format($export->successful_rows) . ' ' . str('ligne')->plural($export->successful_rows) . ' ont été exportées.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('ligne')->plural($failedRowsCount) . ' ont échoué.';
        }

        return $body;
    }
}
