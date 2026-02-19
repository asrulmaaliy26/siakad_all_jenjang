<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MataPelajaranKurikulum;
use Illuminate\Auth\Access\HandlesAuthorization;

class MataPelajaranKurikulumPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MataPelajaranKurikulum');
    }

    public function view(AuthUser $authUser, MataPelajaranKurikulum $mataPelajaranKurikulum): bool
    {
        return $authUser->can('View:MataPelajaranKurikulum');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MataPelajaranKurikulum');
    }

    public function update(AuthUser $authUser, MataPelajaranKurikulum $mataPelajaranKurikulum): bool
    {
        return $authUser->can('Update:MataPelajaranKurikulum');
    }

    public function delete(AuthUser $authUser, MataPelajaranKurikulum $mataPelajaranKurikulum): bool
    {
        return $authUser->can('Delete:MataPelajaranKurikulum');
    }

    public function restore(AuthUser $authUser, MataPelajaranKurikulum $mataPelajaranKurikulum): bool
    {
        return $authUser->can('Restore:MataPelajaranKurikulum');
    }

    public function forceDelete(AuthUser $authUser, MataPelajaranKurikulum $mataPelajaranKurikulum): bool
    {
        return $authUser->can('ForceDelete:MataPelajaranKurikulum');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MataPelajaranKurikulum');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MataPelajaranKurikulum');
    }

    public function replicate(AuthUser $authUser, MataPelajaranKurikulum $mataPelajaranKurikulum): bool
    {
        return $authUser->can('Replicate:MataPelajaranKurikulum');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MataPelajaranKurikulum');
    }

}