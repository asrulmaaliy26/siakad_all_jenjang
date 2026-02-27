<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ReferenceOption;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReferenceOptionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ReferenceOption');
    }

    public function view(AuthUser $authUser, ReferenceOption $referenceOption): bool
    {
        return $authUser->can('View:ReferenceOption');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ReferenceOption');
    }

    public function update(AuthUser $authUser, ReferenceOption $referenceOption): bool
    {
        return $authUser->can('Update:ReferenceOption');
    }

    public function delete(AuthUser $authUser, ReferenceOption $referenceOption): bool
    {
        return $authUser->can('Delete:ReferenceOption');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ReferenceOption');
    }

    public function restore(AuthUser $authUser, ReferenceOption $referenceOption): bool
    {
        return $authUser->can('Restore:ReferenceOption');
    }

    public function forceDelete(AuthUser $authUser, ReferenceOption $referenceOption): bool
    {
        return $authUser->can('ForceDelete:ReferenceOption');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ReferenceOption');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ReferenceOption');
    }

    public function replicate(AuthUser $authUser, ReferenceOption $referenceOption): bool
    {
        return $authUser->can('Replicate:ReferenceOption');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ReferenceOption');
    }

}