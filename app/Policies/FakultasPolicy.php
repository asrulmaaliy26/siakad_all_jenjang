<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Fakultas;
use Illuminate\Auth\Access\HandlesAuthorization;

class FakultasPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Fakultas');
    }

    public function view(AuthUser $authUser, Fakultas $fakultas): bool
    {
        return $authUser->can('View:Fakultas');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Fakultas');
    }

    public function update(AuthUser $authUser, Fakultas $fakultas): bool
    {
        return $authUser->can('Update:Fakultas');
    }

    public function delete(AuthUser $authUser, Fakultas $fakultas): bool
    {
        return $authUser->can('Delete:Fakultas');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Fakultas');
    }

    public function restore(AuthUser $authUser, Fakultas $fakultas): bool
    {
        return $authUser->can('Restore:Fakultas');
    }

    public function forceDelete(AuthUser $authUser, Fakultas $fakultas): bool
    {
        return $authUser->can('ForceDelete:Fakultas');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Fakultas');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Fakultas');
    }

    public function replicate(AuthUser $authUser, Fakultas $fakultas): bool
    {
        return $authUser->can('Replicate:Fakultas');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Fakultas');
    }

}