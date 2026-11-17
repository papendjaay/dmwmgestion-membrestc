<?php

namespace App\Filament\Resources\Enrolements\Pages;

use App\Filament\Resources\Enrolements\EnrolementResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEnrolement extends ViewRecord
{
    protected static string $resource = EnrolementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
