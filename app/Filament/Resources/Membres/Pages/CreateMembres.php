<?php

namespace App\Filament\Resources\Membres\Pages;

use App\Filament\Resources\Membres\MembresResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateMembres extends CreateRecord
{
    protected static string $resource = MembresResource::class;
    #[Override]
    protected function getFormActions(): array
    {
        return [];
    }
    protected static bool $canCreateAnother = false;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
