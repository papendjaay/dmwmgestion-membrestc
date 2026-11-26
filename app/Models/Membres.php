<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Membres extends Model
{
    protected $fillable = [
        'internal_id',
        'Prenom',
        'Nom',
        'Genre',
        'Age',
        'Niveau_Etude',
        'Profession',
        'Conseil',
        'Section',
        'Telephone',
        'Adresse',
        'Type_piece_identite',
        'Numero_piece_identite',
        'Parent',
        'Lien_parental',
        'Contact',
        'Cree_par',
        'Statut',
    ];

    /**
     * Sécurité automatique au niveau du modèle (Cloisonnement & Traçabilité).
     */
    protected static function booted(): void
    {
        static::creating(function (Membres $membre): void {
            static::creating(function (Membres $membre): void {
                if (! Auth::check()) {
                    return;
                }

                $user = Auth::user();
                $membre->Cree_par = $user->name;

                $isSuperAdmin = method_exists($user, 'hasRole')
                    && $user->hasRole(['Super Admin', 'super_admin']);

                if (! $isSuperAdmin) {
                    $maSection = method_exists($user, 'getMaSection') ? $user->getMaSection() : null;

                    if ($maSection) {
                        $membre->Section = $maSection->Nom ?? $membre->Section;
                        $membre->Conseil = $maSection->conseil?->Nom ?? $membre->Conseil;
                    }
                }
            });
        });

        static::addGlobalScope('section_isolation', function (Builder $builder) {
            if (app()->runningInConsole() || ! Auth::check()) {
                return;
            }

            $user = Auth::user();

            if (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin') || $user->hasRole('super_admin'))) {
                return;
            }

            $maSection = method_exists($user, 'getMaSection') ? $user->getMaSection() : null;

            if ($maSection && ! empty($maSection->Nom)) {
                $builder->whereRaw('LOWER(TRIM(Section)) = ?', [mb_strtolower(trim($maSection->Nom))]);
            } else {
                $builder->whereRaw('1 = 0');
            }
        });
    }
}
