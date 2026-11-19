<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Enrolement;
use Illuminate\Auth\Access\HandlesAuthorization;

class EnrolementPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Enrolement');
    }

    public function view(AuthUser $authUser, Enrolement $enrolement): bool
    {
        return $authUser->can('View:Enrolement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Enrolement');
    }

    public function update(AuthUser $authUser, Enrolement $enrolement): bool
    {
        return $authUser->can('Update:Enrolement');
    }

    public function delete(AuthUser $authUser, Enrolement $enrolement): bool
    {
        return $authUser->can('Delete:Enrolement');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Enrolement');
    }

    public function restore(AuthUser $authUser, Enrolement $enrolement): bool
    {
        return $authUser->can('Restore:Enrolement');
    }

    public function forceDelete(AuthUser $authUser, Enrolement $enrolement): bool
    {
        return $authUser->can('ForceDelete:Enrolement');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Enrolement');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Enrolement');
    }

    public function replicate(AuthUser $authUser, Enrolement $enrolement): bool
    {
        return $authUser->can('Replicate:Enrolement');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Enrolement');
    }

}