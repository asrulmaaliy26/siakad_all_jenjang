<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PengajuanSurat;
use Illuminate\Auth\Access\HandlesAuthorization;

class PengajuanSuratPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PengajuanSurat');
    }

    public function view(AuthUser $authUser, PengajuanSurat $pengajuanSurat): bool
    {
        return $authUser->can('View:PengajuanSurat');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PengajuanSurat');
    }

    public function update(AuthUser $authUser, PengajuanSurat $pengajuanSurat): bool
    {
        return $authUser->can('Update:PengajuanSurat');
    }

    public function delete(AuthUser $authUser, PengajuanSurat $pengajuanSurat): bool
    {
        return $authUser->can('Delete:PengajuanSurat');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PengajuanSurat');
    }

    public function restore(AuthUser $authUser, PengajuanSurat $pengajuanSurat): bool
    {
        return $authUser->can('Restore:PengajuanSurat');
    }

    public function forceDelete(AuthUser $authUser, PengajuanSurat $pengajuanSurat): bool
    {
        return $authUser->can('ForceDelete:PengajuanSurat');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PengajuanSurat');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PengajuanSurat');
    }

    public function replicate(AuthUser $authUser, PengajuanSurat $pengajuanSurat): bool
    {
        return $authUser->can('Replicate:PengajuanSurat');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PengajuanSurat');
    }

}