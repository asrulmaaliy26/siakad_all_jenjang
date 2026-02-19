<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SiswaDataPendaftar;
use Illuminate\Auth\Access\HandlesAuthorization;

class SiswaDataPendaftarPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SiswaDataPendaftar');
    }

    public function view(AuthUser $authUser, SiswaDataPendaftar $siswaDataPendaftar): bool
    {
        return $authUser->can('View:SiswaDataPendaftar');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SiswaDataPendaftar');
    }

    public function update(AuthUser $authUser, SiswaDataPendaftar $siswaDataPendaftar): bool
    {
        return $authUser->can('Update:SiswaDataPendaftar');
    }

    public function delete(AuthUser $authUser, SiswaDataPendaftar $siswaDataPendaftar): bool
    {
        return $authUser->can('Delete:SiswaDataPendaftar');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SiswaDataPendaftar');
    }

    public function restore(AuthUser $authUser, SiswaDataPendaftar $siswaDataPendaftar): bool
    {
        return $authUser->can('Restore:SiswaDataPendaftar');
    }

    public function forceDelete(AuthUser $authUser, SiswaDataPendaftar $siswaDataPendaftar): bool
    {
        return $authUser->can('ForceDelete:SiswaDataPendaftar');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SiswaDataPendaftar');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SiswaDataPendaftar');
    }

    public function replicate(AuthUser $authUser, SiswaDataPendaftar $siswaDataPendaftar): bool
    {
        return $authUser->can('Replicate:SiswaDataPendaftar');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SiswaDataPendaftar');
    }
}
