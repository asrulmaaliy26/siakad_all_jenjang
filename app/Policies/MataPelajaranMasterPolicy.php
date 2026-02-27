<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\MataPelajaranMaster;
use Illuminate\Auth\Access\HandlesAuthorization;

class MataPelajaranMasterPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MataPelajaranMaster');
    }

    public function view(AuthUser $authUser, MataPelajaranMaster $mataPelajaranMaster): bool
    {
        return $authUser->can('View:MataPelajaranMaster');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MataPelajaranMaster');
    }

    public function update(AuthUser $authUser, MataPelajaranMaster $mataPelajaranMaster): bool
    {
        return $authUser->can('Update:MataPelajaranMaster');
    }

    public function delete(AuthUser $authUser, MataPelajaranMaster $mataPelajaranMaster): bool
    {
        return $authUser->can('Delete:MataPelajaranMaster');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MataPelajaranMaster');
    }

    public function restore(AuthUser $authUser, MataPelajaranMaster $mataPelajaranMaster): bool
    {
        return $authUser->can('Restore:MataPelajaranMaster');
    }

    public function forceDelete(AuthUser $authUser, MataPelajaranMaster $mataPelajaranMaster): bool
    {
        return $authUser->can('ForceDelete:MataPelajaranMaster');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MataPelajaranMaster');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MataPelajaranMaster');
    }

    public function replicate(AuthUser $authUser, MataPelajaranMaster $mataPelajaranMaster): bool
    {
        return $authUser->can('Replicate:MataPelajaranMaster');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MataPelajaranMaster');
    }

}