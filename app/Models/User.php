<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Récupère la section rattachée au Point Focal connecté.
     */
    public function getMaSection(): ?Section
    {
        $nomUtilisateur = mb_strtolower(trim((string) $this->name));

        if (empty($nomUtilisateur)) {
            return null;
        }

        return Section::with('conseil')
            ->where(function ($query) use ($nomUtilisateur) {
                $query->whereRaw('LOWER(TRIM(PF1)) = ?', [$nomUtilisateur])
                    ->orWhereRaw('LOWER(TRIM(PF2)) = ?', [$nomUtilisateur])
                    ->orWhereRaw('LOWER(TRIM(PF3)) = ?', [$nomUtilisateur]);
            })
            ->first();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
