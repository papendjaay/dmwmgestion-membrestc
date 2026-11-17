<?php

namespace App\Providers;

use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
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
    }
}
