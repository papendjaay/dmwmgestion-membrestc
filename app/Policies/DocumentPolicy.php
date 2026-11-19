<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class DocumentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return true;
    }

    public function create(AuthUser $authUser): bool
    {
        return true;
    }

    public function view(AuthUser $authUser, Document $document): bool
    {
        return true;
    }

    public function update(AuthUser $authUser, Document $document): bool
    {
        return $authUser instanceof User && $authUser->hasRole('super_admin');
    }

    public function delete(AuthUser $authUser, Document $document): bool
    {
        return $authUser instanceof User && $authUser->hasRole('super_admin');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser instanceof User && $authUser->hasRole('super_admin');
    }

    public function restore(AuthUser $authUser, Document $document): bool
    {
        return $authUser instanceof User && $authUser->hasRole('super_admin');
    }

    public function forceDelete(AuthUser $authUser, Document $document): bool
    {
        return $authUser instanceof User && $authUser->hasRole('super_admin');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser instanceof User && $authUser->hasRole('super_admin');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser instanceof User && $authUser->hasRole('super_admin');
    }

    public function replicate(AuthUser $authUser, Document $document): bool
    {
        return $authUser instanceof User && $authUser->hasRole('super_admin');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser instanceof User && $authUser->hasRole('super_admin');
    }
}
