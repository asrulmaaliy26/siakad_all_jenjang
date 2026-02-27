<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ReferalCode;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReferalCodePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ReferalCode');
    }

    public function view(AuthUser $authUser, ReferalCode $referalCode): bool
    {
        return $authUser->can('View:ReferalCode');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ReferalCode');
    }

    public function update(AuthUser $authUser, ReferalCode $referalCode): bool
    {
        return $authUser->can('Update:ReferalCode');
    }

    public function delete(AuthUser $authUser, ReferalCode $referalCode): bool
    {
        return $authUser->can('Delete:ReferalCode');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ReferalCode');
    }

    public function restore(AuthUser $authUser, ReferalCode $referalCode): bool
    {
        return $authUser->can('Restore:ReferalCode');
    }

    public function forceDelete(AuthUser $authUser, ReferalCode $referalCode): bool
    {
        return $authUser->can('ForceDelete:ReferalCode');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ReferalCode');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ReferalCode');
    }

    public function replicate(AuthUser $authUser, ReferalCode $referalCode): bool
    {
        return $authUser->can('Replicate:ReferalCode');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ReferalCode');
    }

}