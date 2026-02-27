<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PeriodeWisuda;
use Illuminate\Auth\Access\HandlesAuthorization;

class PeriodeWisudaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PeriodeWisuda');
    }

    public function view(AuthUser $authUser, PeriodeWisuda $periodeWisuda): bool
    {
        return $authUser->can('View:PeriodeWisuda');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PeriodeWisuda');
    }

    public function update(AuthUser $authUser, PeriodeWisuda $periodeWisuda): bool
    {
        return $authUser->can('Update:PeriodeWisuda');
    }

    public function delete(AuthUser $authUser, PeriodeWisuda $periodeWisuda): bool
    {
        return $authUser->can('Delete:PeriodeWisuda');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PeriodeWisuda');
    }

    public function restore(AuthUser $authUser, PeriodeWisuda $periodeWisuda): bool
    {
        return $authUser->can('Restore:PeriodeWisuda');
    }

    public function forceDelete(AuthUser $authUser, PeriodeWisuda $periodeWisuda): bool
    {
        return $authUser->can('ForceDelete:PeriodeWisuda');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PeriodeWisuda');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PeriodeWisuda');
    }

    public function replicate(AuthUser $authUser, PeriodeWisuda $periodeWisuda): bool
    {
        return $authUser->can('Replicate:PeriodeWisuda');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PeriodeWisuda');
    }

}