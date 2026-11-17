<?php

namespace App\Filament\Resources\Enrolements\Pages;

use App\Filament\Resources\Enrolements\EnrolementResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateEnrolement extends CreateRecord
{
    protected static string $resource = EnrolementResource::class;

    // Redirige directement vers la table Enrôlements après avoir cliqué sur "Créer"
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getFormActions(): array
    {
        return [];
    }
    // Vide tous les champs si l'utilisateur clique sur "Créer & Ajouter un autre"
    protected function afterCreate(): void
    {
        $this->form->fill();
    }

    // Remplit automatiquement l'agent créateur et force sa section si ce n'est pas un super_admin
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        $data['Cree_par'] = $user?->name;

        if ($user && method_exists($user, 'hasRole') && ! $user->hasRole('super_admin') && method_exists($user, 'getMaSection')) {
            $section = $user->getMaSection();
            if ($section) {
                $data['Section'] = $section->Nom;
                $data['Conseil'] = $section->conseil?->Nom ?? $data['Conseil'] ?? null;
            }
        }

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Enrôlement enregistré')
            ->body('La personne a été ajoutée à votre section avec succès.');
    }
}
