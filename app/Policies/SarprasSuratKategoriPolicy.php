<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SarprasSuratKategori;
use Illuminate\Auth\Access\HandlesAuthorization;

class SarprasSuratKategoriPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SarprasSuratKategori');
    }

    public function view(AuthUser $authUser, SarprasSuratKategori $sarprasSuratKategori): bool
    {
        return $authUser->can('View:SarprasSuratKategori');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SarprasSuratKategori');
    }

    public function update(AuthUser $authUser, SarprasSuratKategori $sarprasSuratKategori): bool
    {
        return $authUser->can('Update:SarprasSuratKategori');
    }

    public function delete(AuthUser $authUser, SarprasSuratKategori $sarprasSuratKategori): bool
    {
        return $authUser->can('Delete:SarprasSuratKategori');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SarprasSuratKategori');
    }

    public function restore(AuthUser $authUser, SarprasSuratKategori $sarprasSuratKategori): bool
    {
        return $authUser->can('Restore:SarprasSuratKategori');
    }

    public function forceDelete(AuthUser $authUser, SarprasSuratKategori $sarprasSuratKategori): bool
    {
        return $authUser->can('ForceDelete:SarprasSuratKategori');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SarprasSuratKategori');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SarprasSuratKategori');
    }

    public function replicate(AuthUser $authUser, SarprasSuratKategori $sarprasSuratKategori): bool
    {
        return $authUser->can('Replicate:SarprasSuratKategori');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SarprasSuratKategori');
    }

}