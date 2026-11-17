<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Membres;
use Illuminate\Auth\Access\HandlesAuthorization;

class MembresPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Membres');
    }

    public function view(AuthUser $authUser, Membres $membres): bool
    {
        return $authUser->can('View:Membres');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Membres');
    }

    public function update(AuthUser $authUser, Membres $membres): bool
    {
        return $authUser->can('Update:Membres');
    }

    public function delete(AuthUser $authUser, Membres $membres): bool
    {
        return $authUser->can('Delete:Membres');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Membres');
    }

    public function restore(AuthUser $authUser, Membres $membres): bool
    {
        return $authUser->can('Restore:Membres');
    }

    public function forceDelete(AuthUser $authUser, Membres $membres): bool
    {
        return $authUser->can('ForceDelete:Membres');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Membres');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Membres');
    }

    public function replicate(AuthUser $authUser, Membres $membres): bool
    {
        return $authUser->can('Replicate:Membres');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Membres');
    }

}