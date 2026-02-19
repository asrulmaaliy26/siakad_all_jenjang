<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SiswaDataLJK;
use Illuminate\Auth\Access\HandlesAuthorization;

class SiswaDataLJKPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SiswaDataLJK');
    }

    public function view(AuthUser $authUser, SiswaDataLJK $siswaDataLJK): bool
    {
        return $authUser->can('View:SiswaDataLJK');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SiswaDataLJK');
    }

    public function update(AuthUser $authUser, SiswaDataLJK $siswaDataLJK): bool
    {
        return $authUser->can('Update:SiswaDataLJK');
    }

    public function delete(AuthUser $authUser, SiswaDataLJK $siswaDataLJK): bool
    {
        return $authUser->can('Delete:SiswaDataLJK');
    }

    public function restore(AuthUser $authUser, SiswaDataLJK $siswaDataLJK): bool
    {
        return $authUser->can('Restore:SiswaDataLJK');
    }

    public function forceDelete(AuthUser $authUser, SiswaDataLJK $siswaDataLJK): bool
    {
        return $authUser->can('ForceDelete:SiswaDataLJK');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SiswaDataLJK');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SiswaDataLJK');
    }

    public function replicate(AuthUser $authUser, SiswaDataLJK $siswaDataLJK): bool
    {
        return $authUser->can('Replicate:SiswaDataLJK');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SiswaDataLJK');
    }

}