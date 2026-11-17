<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Notifications\MagicLoginLinkNotification; // <-- Ajout de l'import
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login;
use Filament\Forms\Components\TextInput;
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
                TextInput::make('email')
                    ->label('Adresse e-mail')
                    ->placeholder('exemple@domaine.com')
                    ->email()
                    ->required()
                    ->autocomplete()
                    ->autofocus(),
            ]);
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            Notification::sendNow($user, new MagicLoginLinkNotification());
        }

        // Message affiché même si l'e-mail n'existe pas (sécurité anti-énumération)
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
