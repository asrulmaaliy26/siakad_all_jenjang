<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AbsensiSiswa;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbsensiSiswaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AbsensiSiswa');
    }

    public function view(AuthUser $authUser, AbsensiSiswa $absensiSiswa): bool
    {
        return $authUser->can('View:AbsensiSiswa');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AbsensiSiswa');
    }

    public function update(AuthUser $authUser, AbsensiSiswa $absensiSiswa): bool
    {
        return $authUser->can('Update:AbsensiSiswa');
    }

    public function delete(AuthUser $authUser, AbsensiSiswa $absensiSiswa): bool
    {
        return $authUser->can('Delete:AbsensiSiswa');
    }

    public function restore(AuthUser $authUser, AbsensiSiswa $absensiSiswa): bool
    {
        return $authUser->can('Restore:AbsensiSiswa');
    }

    public function forceDelete(AuthUser $authUser, AbsensiSiswa $absensiSiswa): bool
    {
        return $authUser->can('ForceDelete:AbsensiSiswa');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AbsensiSiswa');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AbsensiSiswa');
    }

    public function replicate(AuthUser $authUser, AbsensiSiswa $absensiSiswa): bool
    {
        return $authUser->can('Replicate:AbsensiSiswa');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AbsensiSiswa');
    }

}