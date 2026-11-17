<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Retour')
                ->url(UserResource::getUrl('index'))
                ->color('sucess')
                ->icon('heroicon-o-arrow-left'),
            EditAction::make(),
        ];
    }
}
