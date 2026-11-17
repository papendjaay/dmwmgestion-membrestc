<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Conseil;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConseilPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Conseil');
    }

    public function view(AuthUser $authUser, Conseil $conseil): bool
    {
        return $authUser->can('View:Conseil');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Conseil');
    }

    public function update(AuthUser $authUser, Conseil $conseil): bool
    {
        return $authUser->can('Update:Conseil');
    }

    public function delete(AuthUser $authUser, Conseil $conseil): bool
    {
        return $authUser->can('Delete:Conseil');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Conseil');
    }

    public function restore(AuthUser $authUser, Conseil $conseil): bool
    {
        return $authUser->can('Restore:Conseil');
    }

    public function forceDelete(AuthUser $authUser, Conseil $conseil): bool
    {
        return $authUser->can('ForceDelete:Conseil');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Conseil');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Conseil');
    }

    public function replicate(AuthUser $authUser, Conseil $conseil): bool
    {
        return $authUser->can('Replicate:Conseil');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Conseil');
    }

}