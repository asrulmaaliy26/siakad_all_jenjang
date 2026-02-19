<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SiswaDataOrangTua;
use Illuminate\Auth\Access\HandlesAuthorization;

class SiswaDataOrangTuaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SiswaDataOrangTua');
    }

    public function view(AuthUser $authUser, SiswaDataOrangTua $siswaDataOrangTua): bool
    {
        return $authUser->can('View:SiswaDataOrangTua');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SiswaDataOrangTua');
    }

    public function update(AuthUser $authUser, SiswaDataOrangTua $siswaDataOrangTua): bool
    {
        return $authUser->can('Update:SiswaDataOrangTua');
    }

    public function delete(AuthUser $authUser, SiswaDataOrangTua $siswaDataOrangTua): bool
    {
        return $authUser->can('Delete:SiswaDataOrangTua');
    }

    public function restore(AuthUser $authUser, SiswaDataOrangTua $siswaDataOrangTua): bool
    {
        return $authUser->can('Restore:SiswaDataOrangTua');
    }

    public function forceDelete(AuthUser $authUser, SiswaDataOrangTua $siswaDataOrangTua): bool
    {
        return $authUser->can('ForceDelete:SiswaDataOrangTua');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SiswaDataOrangTua');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SiswaDataOrangTua');
    }

    public function replicate(AuthUser $authUser, SiswaDataOrangTua $siswaDataOrangTua): bool
    {
        return $authUser->can('Replicate:SiswaDataOrangTua');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SiswaDataOrangTua');
    }

}