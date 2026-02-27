<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MataPelajaranKelasDistribusi;
use Illuminate\Auth\Access\HandlesAuthorization;

class MataPelajaranKelasDistribusiPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MataPelajaranKelasDistribusi');
    }

    public function view(AuthUser $authUser, MataPelajaranKelasDistribusi $mataPelajaranKelasDistribusi): bool
    {
        return $authUser->can('View:MataPelajaranKelasDistribusi');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MataPelajaranKelasDistribusi');
    }

    public function update(AuthUser $authUser, MataPelajaranKelasDistribusi $mataPelajaranKelasDistribusi): bool
    {
        return $authUser->can('Update:MataPelajaranKelasDistribusi');
    }

    public function delete(AuthUser $authUser, MataPelajaranKelasDistribusi $mataPelajaranKelasDistribusi): bool
    {
        return $authUser->can('Delete:MataPelajaranKelasDistribusi');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MataPelajaranKelasDistribusi');
    }

    public function restore(AuthUser $authUser, MataPelajaranKelasDistribusi $mataPelajaranKelasDistribusi): bool
    {
        return $authUser->can('Restore:MataPelajaranKelasDistribusi');
    }

    public function forceDelete(AuthUser $authUser, MataPelajaranKelasDistribusi $mataPelajaranKelasDistribusi): bool
    {
        return $authUser->can('ForceDelete:MataPelajaranKelasDistribusi');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MataPelajaranKelasDistribusi');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MataPelajaranKelasDistribusi');
    }

    public function replicate(AuthUser $authUser, MataPelajaranKelasDistribusi $mataPelajaranKelasDistribusi): bool
    {
        return $authUser->can('Replicate:MataPelajaranKelasDistribusi');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MataPelajaranKelasDistribusi');
    }

}