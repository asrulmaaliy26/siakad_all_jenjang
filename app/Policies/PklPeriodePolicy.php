<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PklPeriode;
use Illuminate\Auth\Access\HandlesAuthorization;

class PklPeriodePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function view(AuthUser $authUser, PklPeriode $pklPeriode): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function update(AuthUser $authUser, PklPeriode $pklPeriode): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function delete(AuthUser $authUser, PklPeriode $pklPeriode): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function restore(AuthUser $authUser, PklPeriode $pklPeriode): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function forceDelete(AuthUser $authUser, PklPeriode $pklPeriode): bool
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

    public function replicate(AuthUser $authUser, PklPeriode $pklPeriode): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->hasAnyRole(['super_admin', 'admin', 'kaprodi']);
    }

}