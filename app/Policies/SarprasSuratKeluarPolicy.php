<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SarprasSuratKeluar;
use Illuminate\Auth\Access\HandlesAuthorization;

class SarprasSuratKeluarPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SarprasSuratKeluar');
    }

    public function view(AuthUser $authUser, SarprasSuratKeluar $sarprasSuratKeluar): bool
    {
        return $authUser->can('View:SarprasSuratKeluar');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SarprasSuratKeluar');
    }

    public function update(AuthUser $authUser, SarprasSuratKeluar $sarprasSuratKeluar): bool
    {
        return $authUser->can('Update:SarprasSuratKeluar');
    }

    public function delete(AuthUser $authUser, SarprasSuratKeluar $sarprasSuratKeluar): bool
    {
        return $authUser->can('Delete:SarprasSuratKeluar');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SarprasSuratKeluar');
    }

    public function restore(AuthUser $authUser, SarprasSuratKeluar $sarprasSuratKeluar): bool
    {
        return $authUser->can('Restore:SarprasSuratKeluar');
    }

    public function forceDelete(AuthUser $authUser, SarprasSuratKeluar $sarprasSuratKeluar): bool
    {
        return $authUser->can('ForceDelete:SarprasSuratKeluar');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SarprasSuratKeluar');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SarprasSuratKeluar');
    }

    public function replicate(AuthUser $authUser, SarprasSuratKeluar $sarprasSuratKeluar): bool
    {
        return $authUser->can('Replicate:SarprasSuratKeluar');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SarprasSuratKeluar');
    }

}