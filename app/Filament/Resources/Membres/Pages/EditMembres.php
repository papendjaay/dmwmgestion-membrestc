<?php

namespace App\Filament\Resources\Membres\Pages;

use App\Filament\Resources\Membres\MembresResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMembres extends EditRecord
{
    protected static string $resource = MembresResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
