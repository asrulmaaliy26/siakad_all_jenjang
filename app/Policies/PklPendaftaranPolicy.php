<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PklPendaftaran;
use Illuminate\Auth\Access\HandlesAuthorization;

class PklPendaftaranPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi', 'murid']);
    }

    public function view(AuthUser $authUser, PklPendaftaran $pklPendaftaran): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi', 'murid']);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi', 'murid']);
    }

    public function update(AuthUser $authUser, PklPendaftaran $pklPendaftaran): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi', 'murid']);
    }

    public function delete(AuthUser $authUser, PklPendaftaran $pklPendaftaran): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function restore(AuthUser $authUser, PklPendaftaran $pklPendaftaran): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function forceDelete(AuthUser $authUser, PklPendaftaran $pklPendaftaran): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function replicate(AuthUser $authUser, PklPendaftaran $pklPendaftaran): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

}