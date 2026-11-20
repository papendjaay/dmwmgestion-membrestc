<?php

namespace App\Providers;

use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
    }
}
