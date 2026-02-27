<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PengaturanPendaftaran;
use Illuminate\Auth\Access\HandlesAuthorization;

class PengaturanPendaftaranPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PengaturanPendaftaran');
    }

    public function view(AuthUser $authUser, PengaturanPendaftaran $pengaturanPendaftaran): bool
    {
        return $authUser->can('View:PengaturanPendaftaran');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PengaturanPendaftaran');
    }

    public function update(AuthUser $authUser, PengaturanPendaftaran $pengaturanPendaftaran): bool
    {
        return $authUser->can('Update:PengaturanPendaftaran');
    }

    public function delete(AuthUser $authUser, PengaturanPendaftaran $pengaturanPendaftaran): bool
    {
        return $authUser->can('Delete:PengaturanPendaftaran');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PengaturanPendaftaran');
    }

    public function restore(AuthUser $authUser, PengaturanPendaftaran $pengaturanPendaftaran): bool
    {
        return $authUser->can('Restore:PengaturanPendaftaran');
    }

    public function forceDelete(AuthUser $authUser, PengaturanPendaftaran $pengaturanPendaftaran): bool
    {
        return $authUser->can('ForceDelete:PengaturanPendaftaran');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PengaturanPendaftaran');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PengaturanPendaftaran');
    }

    public function replicate(AuthUser $authUser, PengaturanPendaftaran $pengaturanPendaftaran): bool
    {
        return $authUser->can('Replicate:PengaturanPendaftaran');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PengaturanPendaftaran');
    }

}