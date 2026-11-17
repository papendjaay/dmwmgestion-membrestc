<?php

namespace App\Filament\Resources\Conseils\Pages;

use App\Filament\Resources\Conseils\ConseilResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Pages\ViewRecord;

class ViewConseil extends ViewRecord
{
    protected static string $resource = ConseilResource::class;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist
            // Ajout d'un arrière-plan global sur toute la zone de l'infolist via Tailwind CSS
            // Ici : Un dégradé subtil allant du bleu très clair au blanc (s'adapte aussi au mode sombre)
            ->extraAttributes([
                'class' => 'bg-slate-100 dark:bg-slate-950 p-6 rounded-xl border border-gray-200 dark:border-slate-900 shadow-sm'
            ])
            ->schema([

                // CADRE 1 : Informations Générales
                Section::make('Informations Générales')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextEntry::make('Nom')
                            ->label('Nom')
                            ->weight('bold'),
                        TextEntry::make('Code')
                            ->label('Code'),
                    ])->columns(2),

                // CADRE 2 : Points Focaux & Contacts
                Section::make('Points Focaux & Contacts')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        // Ligne PF1
                        TextEntry::make('PF1')
                            ->label('Point Focal 1')
                            ->color('success'),
                        TextEntry::make('Tel-PF1')
                            ->label('Téléphone PF1')
                            ->badge()
                            ->color('info'),

                        // Ligne PF2
                        TextEntry::make('PF2')
                            ->label('Point Focal 2')
                            ->color('success'),
                        TextEntry::make('Tel-PF2')
                            ->label('Téléphone PF2')
                            ->badge()
                            ->color('info'),

                        // Ligne PF3
                        TextEntry::make('PF3')
                            ->label('Point Focal 3')
                            ->color('success'),
                        TextEntry::make('Tel-PF3')
                            ->label('Téléphone PF3')
                            ->badge()
                            ->color('info'),
                    ])->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Retour')
                ->url(ConseilResource::getUrl('index'))
                ->color('success') // Correction de l'orthographe 'success'
                ->icon('heroicon-o-arrow-left'),
            EditAction::make(),
        ];
    }
}
