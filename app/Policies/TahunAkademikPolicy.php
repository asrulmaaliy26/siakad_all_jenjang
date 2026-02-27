<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TahunAkademik;
use Illuminate\Auth\Access\HandlesAuthorization;

class TahunAkademikPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TahunAkademik');
    }

    public function view(AuthUser $authUser, TahunAkademik $tahunAkademik): bool
    {
        return $authUser->can('View:TahunAkademik');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TahunAkademik');
    }

    public function update(AuthUser $authUser, TahunAkademik $tahunAkademik): bool
    {
        return $authUser->can('Update:TahunAkademik');
    }

    public function delete(AuthUser $authUser, TahunAkademik $tahunAkademik): bool
    {
        return $authUser->can('Delete:TahunAkademik');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TahunAkademik');
    }

    public function restore(AuthUser $authUser, TahunAkademik $tahunAkademik): bool
    {
        return $authUser->can('Restore:TahunAkademik');
    }

    public function forceDelete(AuthUser $authUser, TahunAkademik $tahunAkademik): bool
    {
        return $authUser->can('ForceDelete:TahunAkademik');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TahunAkademik');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TahunAkademik');
    }

    public function replicate(AuthUser $authUser, TahunAkademik $tahunAkademik): bool
    {
        return $authUser->can('Replicate:TahunAkademik');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TahunAkademik');
    }

}