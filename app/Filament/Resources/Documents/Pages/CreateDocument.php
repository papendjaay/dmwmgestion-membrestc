<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTop')
                ->label('Créer')
                ->icon('heroicon-m-check')
                ->color('primary')
                ->action('create'),
        ];
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Document partagé avec succès à tous les utilisateurs';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
