<?php

namespace App\Providers;

use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });
        // Déplace "Roles" dans votre groupe "Sécurité & Accès"
        // RoleResource::navigationGroup('Sécurité & Accès');
        RoleResource::navigationSort(2);
        if (str_contains(request()->getHost(), 'devtunnels.ms')) {
            URL::forceScheme('https');
        }
        if (str_contains(request()->getHost(), 'devtunnels.ms')) {
            URL::forceScheme('https');
            request()->server->set('HTTPS', 'on');
        }
        // 1. Imposer des mots de passe robustes partout dans l'application
        Password::defaults(function () {
            return Password::min(12)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });

        // 2. Journaliser chaque connexion réussie avec l'IP
        Event::listen(Login::class, function (Login $event) {
            activity('auth')
                ->causedBy($event->user instanceof \Illuminate\Database\Eloquent\Model ? $event->user : null)
                ->withProperties([
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log('Connexion réussie au panel');
        });

        // 3. Journaliser chaque tentative de connexion échouée (attaque ou erreur)
        Event::listen(Failed::class, function (Failed $event) {
            activity('auth')
                ->withProperties([
                    'email' => $event->credentials['email'] ?? 'inconnu',
                    'ip' => request()->ip(),
                ])
                ->log('Échec de tentative de connexion');
        });
    }
}
