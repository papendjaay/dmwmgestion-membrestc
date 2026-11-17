<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Document;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class DocumentPolicy
{
    use HandlesAuthorization;

    /**
     * 1. Le Super Admin a tous les droits (y compris modifier et supprimer).
     * 2. Les utilisateurs ayant le rôle 'membre' sont totalement bloqués sur cette page.
     */
    public function before(AuthUser $authUser, string $ability): ?bool
    {
        if (method_exists($authUser, 'hasRole')) {
            if ($authUser->hasRole('super_admin')) {
                return true;
            }

            if ($authUser->hasRole('membre')) {
                return false;
            }
        }

        return null;
    }

    /**
     * Les autres rôles (ex. point_focal) peuvent voir la liste des documents.
     */
    public function viewAny(AuthUser $authUser): bool
    {
        return true;
    }

    /**
     * Les autres rôles peuvent consulter et télécharger un document.
     */
    public function view(AuthUser $authUser, Document $document): bool
    {
        return true;
    }

    /**
     * Les autres rôles peuvent créer/partager un nouveau document.
     */
    public function create(AuthUser $authUser): bool
    {
        return true;
    }

    /**
     * Interdit la modification (réservé au super_admin).
     */
    public function update(AuthUser $authUser, Document $document): bool
    {
        return false;
    }

    /**
     * Interdit la suppression (réservé au super_admin).
     */
    public function delete(AuthUser $authUser, Document $document): bool
    {
        return false;
    }

    /**
     * Interdit la suppression groupée (réservé au super_admin).
     */
    public function deleteAny(AuthUser $authUser): bool
    {
        return false;
    }

    public function restore(AuthUser $authUser, Document $document): bool
    {
        return false;
    }

    public function forceDelete(AuthUser $authUser, Document $document): bool
    {
        return false;
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return false;
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return false;
    }
}
