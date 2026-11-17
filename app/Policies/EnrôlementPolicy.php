<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Access\HandlesAuthorization;

class EnrôlementPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Enrôlement');
    }

    public function view(AuthUser $authUser, Model $enrôlement): bool
    {
        return $authUser->can('View:Enrôlement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Enrôlement');
    }

    public function update(AuthUser $authUser, Model $enrôlement): bool
    {
        return $authUser->can('Update:Enrôlement');
    }

    public function delete(AuthUser $authUser, Model $enrôlement): bool
    {
        return $authUser->can('Delete:Enrôlement');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Enrôlement');
    }

    public function restore(AuthUser $authUser, Model $enrôlement): bool
    {
        return $authUser->can('Restore:Enrôlement');
    }

    public function forceDelete(AuthUser $authUser, Model $enrôlement): bool
    {
        return $authUser->can('ForceDelete:Enrôlement');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Enrôlement');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Enrôlement');
    }

    public function replicate(AuthUser $authUser, Model $enrôlement): bool
    {
        return $authUser->can('Replicate:Enrôlement');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Enrôlement');
    }
}
