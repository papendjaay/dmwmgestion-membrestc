<?php

namespace App\Filament\Resources\Enrolements\Pages;

use App\Filament\Resources\Enrolements\EnrolementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ListEnrolements extends ListRecords
{
    protected static string $resource = EnrolementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouvel enrôlement')
                ->icon(Heroicon::OutlinedPlusCircle)
                ->authorize(true)
                ->color('primary')
                ->visible(function (): bool {
                    $user = Auth::user();

                    return $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['super_admin', 'point_focal']);
                }),
        ];
    }
}
