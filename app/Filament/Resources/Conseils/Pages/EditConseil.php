<?php

namespace App\Filament\Resources\Conseils\Pages;

use App\Filament\Resources\Conseils\ConseilResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditConseil extends EditRecord
{
    protected static string $resource = ConseilResource::class;

     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
