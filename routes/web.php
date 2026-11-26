<?php

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ❌ SUPPRIMÉ : Route::redirect('/', '/admin/login');
// Filament gère directement "/" :
// - Non connecté -> redirige automatiquement vers "/login" (CustomLogin)
// - Connecté -> affiche directement votre tableau de bord

// Route sécurisée pour la connexion par lien d'accès (Magic Link)
Route::get('/auth/magic-login/{user}', function (Request $request, User $user) {
    if (! $request->hasValidSignature()) {
        abort(403, 'Ce lien de connexion est invalide ou a expiré.');
    }

    Auth::login($user);
    $request->session()->regenerate();

    // Cherche automatiquement le premier panneau Filament auquel ce rôle a accès
    foreach (Filament::getPanels() as $panel) {
        if ($user->canAccessPanel($panel)) {
            return redirect($panel->getUrl());
        }
    }

    abort(403, 'Aucun panneau accessible pour votre rôle.');
})->name('magic.login');
