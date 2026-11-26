<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Notifications\MagicLoginLinkNotification;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Notification;

class CustomLogin extends Login
{
    protected static string $layout = 'filament-panels::components.layout.base';

    protected string $view = 'filament.pages.auth.custom-login';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent()
                    ->label('Adresse e-mail')
                    ->placeholder('exemple@domaine.com'),
                $this->getPasswordFormComponent()
                    ->label('Mot de passe')
                    ->placeholder('••••••••••'),
                $this->getRememberFormComponent()
                    ->label('Se souvenir de moi'),
            ]);
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            Notification::sendNow($user, new MagicLoginLinkNotification());
        }

        FilamentNotification::make()
            ->title('Lien d\'accès envoyé !')
            ->body('Si cette adresse e-mail est enregistrée, vous allez recevoir un lien pour accéder à votre espace.')
            ->success()
            ->duration(10000)
            ->send();

        $this->form->fill();

        return null;
    }
}
