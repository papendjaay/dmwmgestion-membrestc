<?php

namespace App\Policies;

use App\Models\Membres;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MembresPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_membres');
    }

    public function view(User $user, Membres $membre): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $this->appartientASaSection($user, $membre);
    }

    public function create(User $user): bool
    {
        return $user->can('create_membres');
    }

    public function update(User $user, Membres $membre): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Un Point Focal ne peut pas modifier la fiche système d'un autre Point Focal
        if (str_starts_with((string) $membre->Cree_par, 'Système')) {
            return false;
        }

        return $user->can('update_membres') && $this->appartientASaSection($user, $membre);
    }

    public function delete(User $user, Membres $membre): bool
    {
        // Seul le Super Admin peut supprimer un membre enrôlé
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return false;
    }
    private function appartientASaSection(User $user, Membres $membre): bool
    {
        // Si PF de Conseil : vérifie si le membre appartient à son Conseil
        if ($user->hasRole('point_focal_conseil')) {
            $monConseil = method_exists($user, 'getMonConseil') ? $user->getMonConseil() : null;

            return $monConseil
                && ! empty($monConseil->Nom)
                && mb_strtolower(trim($monConseil->Nom)) === mb_strtolower(trim((string) $membre->Conseil));
        }

        // Si PF de Section : vérifie si le membre appartient à sa Section
        $maSection = method_exists($user, 'getMaSection') ? $user->getMaSection() : null;

        return $maSection
            && ! empty($maSection->Nom)
            && mb_strtolower(trim($maSection->Nom)) === mb_strtolower(trim((string) $membre->Section));
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }
}
