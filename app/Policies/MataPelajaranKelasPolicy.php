<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MataPelajaranKelas;
use Illuminate\Auth\Access\HandlesAuthorization;

class MataPelajaranKelasPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MataPelajaranKelas');
    }

    public function view(AuthUser $authUser, MataPelajaranKelas $mataPelajaranKelas): bool
    {
        return $authUser->can('View:MataPelajaranKelas');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MataPelajaranKelas');
    }

    public function update(AuthUser $authUser, MataPelajaranKelas $mataPelajaranKelas): bool
    {
        return $authUser->can('Update:MataPelajaranKelas');
    }

    public function delete(AuthUser $authUser, MataPelajaranKelas $mataPelajaranKelas): bool
    {
        return $authUser->can('Delete:MataPelajaranKelas');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MataPelajaranKelas');
    }

    public function restore(AuthUser $authUser, MataPelajaranKelas $mataPelajaranKelas): bool
    {
        return $authUser->can('Restore:MataPelajaranKelas');
    }

    public function forceDelete(AuthUser $authUser, MataPelajaranKelas $mataPelajaranKelas): bool
    {
        return $authUser->can('ForceDelete:MataPelajaranKelas');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MataPelajaranKelas');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MataPelajaranKelas');
    }

    public function replicate(AuthUser $authUser, MataPelajaranKelas $mataPelajaranKelas): bool
    {
        return $authUser->can('Replicate:MataPelajaranKelas');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MataPelajaranKelas');
    }
}
