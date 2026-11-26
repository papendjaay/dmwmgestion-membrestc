<?php

namespace App\Models;

use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\Email\Concerns\InteractsWithEmailAuthentication;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements
    FilamentUser,
    HasAppAuthentication,
    HasAppAuthenticationRecovery,
    HasEmailAuthentication
{
    use Notifiable;
    use HasRoles;
    use InteractsWithAppAuthentication;
    use InteractsWithAppAuthenticationRecovery;
    use InteractsWithEmailAuthentication;

    protected $fillable = [
        'name',
        'email',
        'password',
        'section_id',
        'conseil_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'app_authentication_secret' => 'encrypted',
            'app_authentication_recovery_codes' => 'encrypted:array',
            'has_email_authentication' => 'boolean',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function conseil(): BelongsTo
    {
        return $this->belongsTo(Conseil::class);
    }

    public function getMaSection(): ?Section
    {
        if ($this->section_id) {
            return $this->section()->with('conseil')->first();
        }

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

    public function getMonConseil(): ?Conseil
    {
        if ($this->conseil_id) {
            return Conseil::find($this->conseil_id);
        }

        return Conseil::where('email_pf1', $this->email)
            ->orWhere('email_pf2', $this->email)
            ->orWhere('email_pf3', $this->email)
            ->orWhereRaw('LOWER(TRIM(pf1)) = ?', [mb_strtolower(trim($this->name))])
            ->orWhereRaw('LOWER(TRIM(pf2)) = ?', [mb_strtolower(trim($this->name))])
            ->orWhereRaw('LOWER(TRIM(pf3)) = ?', [mb_strtolower(trim($this->name))])
            ->first();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $estActif = $this->is_active ?? true;

        return $estActif && $this->hasAnyRole(['super_admin', 'point_focal', 'point_focal_conseil']);
    }
}
