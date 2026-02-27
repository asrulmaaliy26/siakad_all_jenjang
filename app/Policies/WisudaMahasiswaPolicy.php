<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\WisudaMahasiswa;
use Illuminate\Auth\Access\HandlesAuthorization;

class WisudaMahasiswaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WisudaMahasiswa');
    }

    public function view(AuthUser $authUser, WisudaMahasiswa $wisudaMahasiswa): bool
    {
        return $authUser->can('View:WisudaMahasiswa');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WisudaMahasiswa');
    }

    public function update(AuthUser $authUser, WisudaMahasiswa $wisudaMahasiswa): bool
    {
        return $authUser->can('Update:WisudaMahasiswa');
    }

    public function delete(AuthUser $authUser, WisudaMahasiswa $wisudaMahasiswa): bool
    {
        return $authUser->can('Delete:WisudaMahasiswa');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:WisudaMahasiswa');
    }

    public function restore(AuthUser $authUser, WisudaMahasiswa $wisudaMahasiswa): bool
    {
        return $authUser->can('Restore:WisudaMahasiswa');
    }

    public function forceDelete(AuthUser $authUser, WisudaMahasiswa $wisudaMahasiswa): bool
    {
        return $authUser->can('ForceDelete:WisudaMahasiswa');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:WisudaMahasiswa');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:WisudaMahasiswa');
    }

    public function replicate(AuthUser $authUser, WisudaMahasiswa $wisudaMahasiswa): bool
    {
        return $authUser->can('Replicate:WisudaMahasiswa');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WisudaMahasiswa');
    }

}