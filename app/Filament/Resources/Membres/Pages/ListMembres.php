<?php

namespace App\Filament\Resources\Membres\Pages;

use App\Filament\Resources\Membres\MembresResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMembres extends ListRecords
{
    protected static string $resource = MembresResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),

        ];
    }
}
