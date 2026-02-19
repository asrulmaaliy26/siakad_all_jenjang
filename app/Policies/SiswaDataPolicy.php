<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SiswaData;
use Illuminate\Auth\Access\HandlesAuthorization;

class SiswaDataPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SiswaData');
    }

    public function view(AuthUser $authUser, SiswaData $siswaData): bool
    {
        return $authUser->can('View:SiswaData');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SiswaData');
    }

    public function update(AuthUser $authUser, SiswaData $siswaData): bool
    {
        return $authUser->can('Update:SiswaData');
    }

    public function delete(AuthUser $authUser, SiswaData $siswaData): bool
    {
        return $authUser->can('Delete:SiswaData');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SiswaData');
    }

    public function restore(AuthUser $authUser, SiswaData $siswaData): bool
    {
        return $authUser->can('Restore:SiswaData');
    }

    public function forceDelete(AuthUser $authUser, SiswaData $siswaData): bool
    {
        return $authUser->can('ForceDelete:SiswaData');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SiswaData');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SiswaData');
    }

    public function replicate(AuthUser $authUser, SiswaData $siswaData): bool
    {
        return $authUser->can('Replicate:SiswaData');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SiswaData');
    }
}
