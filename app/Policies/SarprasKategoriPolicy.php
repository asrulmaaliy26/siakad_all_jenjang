<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SarprasKategori;
use Illuminate\Auth\Access\HandlesAuthorization;

class SarprasKategoriPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SarprasKategori');
    }

    public function view(AuthUser $authUser, SarprasKategori $sarprasKategori): bool
    {
        return $authUser->can('View:SarprasKategori');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SarprasKategori');
    }

    public function update(AuthUser $authUser, SarprasKategori $sarprasKategori): bool
    {
        return $authUser->can('Update:SarprasKategori');
    }

    public function delete(AuthUser $authUser, SarprasKategori $sarprasKategori): bool
    {
        return $authUser->can('Delete:SarprasKategori');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SarprasKategori');
    }

    public function restore(AuthUser $authUser, SarprasKategori $sarprasKategori): bool
    {
        return $authUser->can('Restore:SarprasKategori');
    }

    public function forceDelete(AuthUser $authUser, SarprasKategori $sarprasKategori): bool
    {
        return $authUser->can('ForceDelete:SarprasKategori');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SarprasKategori');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SarprasKategori');
    }

    public function replicate(AuthUser $authUser, SarprasKategori $sarprasKategori): bool
    {
        return $authUser->can('Replicate:SarprasKategori');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SarprasKategori');
    }

}