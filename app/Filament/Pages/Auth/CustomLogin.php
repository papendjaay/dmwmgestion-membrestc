<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login;
use Filament\Schemas\Schema;

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
}
