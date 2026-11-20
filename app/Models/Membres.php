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
        // Force l'auteur, la section et le conseil côté serveur à la création.
        static::creating(function (Membres $membre): void {
            if (! Auth::check()) {
                return;
            }

            $user = Auth::user();
            $membre->Cree_par = $user->name;

            $isSuperAdmin = method_exists($user, 'hasRole')
                && ($user->hasRole('Super Admin') || $user->hasRole('super_admin'));

            $maSection = method_exists($user, 'getMaSection')
                ? $user->getMaSection()
                : null;

            if (! $isSuperAdmin && $maSection) {
                $membre->Section = $maSection->nom
                    ?? $maSection->Nom
                    ?? $maSection->Section
                    ?? $membre->Section;
                $membre->Conseil = $maSection->conseil?->nom
                    ?? $maSection->conseil?->Nom
                    ?? $membre->Conseil;
            }
        });

        // 2. Cloisonnement strict : un Point Focal ne voit et ne modifie QUE les membres de sa Section
        static::addGlobalScope('section_isolation', function (Builder $builder) {
            // Ne s'applique pas en console (commandes artisan, seeders)
            if (app()->runningInConsole() || ! Auth::check()) {
                return;
            }

            $user = Auth::user();

            // Les Super Admins gardent une visibilité globale
            if (
                method_exists($user, 'hasRole')
                && ($user->hasRole('Super Admin') || $user->hasRole('super_admin'))
            ) {
                return;
            }
            // 2. Cloisonnement strict : un point_focal ne voit QUE les membres de sa Section
            static::addGlobalScope('section_isolation', function (Builder $builder) {
                if (app()->runningInConsole() || ! Auth::check()) {
                    return;
                }

                $user = Auth::user();

                if (method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
                    return;
                }

                $maSection = method_exists($user, 'getMaSection')
                    ? $user->getMaSection()
                    : null;

                if ($maSection && ! empty($maSection->Nom)) {
                    $builder->whereRaw('LOWER(TRIM(Section)) = ?', [mb_strtolower(trim($maSection->Nom))]);
                } else {
                    $builder->whereRaw('1 = 0');
                }
            });
            $maSection = method_exists($user, 'getMaSection')
                ? $user->getMaSection()
                : null;

            if ($maSection) {
                // Adaptez 'nom' si la colonne du nom de section dans votre table `sections` s'appelle autrement
                $nomSection = $maSection->nom ?? $maSection->Section ?? null;

                if ($nomSection) {
                    $builder->whereRaw('LOWER(TRIM(Section)) = ?', [mb_strtolower(trim($nomSection))]);
                } else {
                    $builder->whereRaw('1 = 0'); // Bloque tout par sécurité si section introuvable
                }
            } else {
                // Aucun accès si l'utilisateur n'est rattaché à aucune section
                $builder->whereRaw('1 = 0');
            }
        });
    }
}
