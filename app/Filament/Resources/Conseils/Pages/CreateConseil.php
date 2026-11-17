<?php

namespace App\Filament\Resources\Conseils\Pages;

use App\Filament\Resources\Conseils\ConseilResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;


class CreateConseil extends CreateRecord
{
    protected static string $resource = ConseilResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
