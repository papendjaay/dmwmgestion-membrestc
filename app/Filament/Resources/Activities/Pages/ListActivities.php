<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reinitialiser_journal')
                ->label('Réinitialiser le journal')
                ->icon('heroicon-o-trash')
                ->color('danger')
                // Visible uniquement pour le super_admin et si le journal n'est pas déjà vide
                ->visible(fn(): bool => Auth::check()
                    && is_callable([Auth::user(), 'hasRole'])
                    && call_user_func([Auth::user(), 'hasRole'], ['super_admin', 'Super Admin'])
                    && Activity::query()->exists())
                // Fenêtre de confirmation obligatoire pour éviter tout clic accidentel
                ->requiresConfirmation()
                ->modalHeading('Réinitialiser le journal d\'audit ?')
                ->modalDescription('Cette action supprimera définitivement tout l\'historique des connexions et modifications enregistrées. Voulez-vous continuer ?')
                ->modalSubmitActionLabel('Oui, tout vider')
                ->action(function (): void {
                    // Vide la table activity_log
                    Activity::query()->delete();

                    // Enregistre une nouvelle entrée indiquant qui a vidé le journal
                    activity('auth')
                        ->causedBy(Auth::user())
                        ->withProperties(['ip' => request()->ip()])
                        ->log('Réinitialisation manuelle du journal d\'audit');

                    Notification::make()
                        ->title('Journal d\'audit réinitialisé')
                        ->body('Toutes les anciennes entrées ont été supprimées.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
