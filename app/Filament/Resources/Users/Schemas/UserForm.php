<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('NOM')
                    ->placeholder('Nom complet')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('EMAIL')
                    ->placeholder('Adresse e-mail')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('MOT DE PASSE')
                    ->placeholder('Mot de passe')
                    ->password()
                    ->revealable()
                    ->rule(Password::min(8)->letters()->mixedCase()->numbers())
                    ->dehydrateStateUsing(fn(?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn(?string $state): bool => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->helperText('Minimum 8 caractères (majuscules, minuscules et chiffres). Laissez vide pour ne pas modifier.'),

                Select::make('roles')
                    ->label('RÔLE ASSIGNÉ')
                    ->relationship('roles', 'name', function ($query) {
                        // Sécurité : seul un super_admin peut voir et attribuer le rôle super_admin
                        $user = Auth::user();

                        if (! $user || ! is_callable([$user, 'hasRole']) || ! call_user_func([$user, 'hasRole'], 'super_admin')) {
                            $query->where('name', '!=', 'super_admin');
                        }
                    })
                    ->placeholder('Rôle assigné')
                    ->preload()
                    ->required()
                    ->searchable(),

                Toggle::make('has_email_authentication')
                    ->label('Double authentification par e-mail (2FA)')
                    ->helperText('Exige un code de vérification envoyé par e-mail lors de la connexion.')
                    ->default(true)
                    ->onColor('success')
                    ->offColor('gray'),
            ]);
    }
}
