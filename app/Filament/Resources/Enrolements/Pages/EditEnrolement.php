<?php

namespace App\Filament\Resources\Enrolements\Pages;

use App\Filament\Resources\Enrolements\EnrolementResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEnrolement extends EditRecord
{
    protected static string $resource = EnrolementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Masque les boutons par défaut en bas de page car le Wizard
     * gère déjà son propre bouton de soumission à l'étape 3.
     */
    protected function getFormActions(): array
    {
        return [];
    }

    /**
     * Redirige vers la liste des enrôlements après la modification.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
