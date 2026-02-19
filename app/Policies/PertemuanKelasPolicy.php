<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PertemuanKelas;
use Illuminate\Auth\Access\HandlesAuthorization;

class PertemuanKelasPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PertemuanKelas');
    }

    public function view(AuthUser $authUser, PertemuanKelas $pertemuanKelas): bool
    {
        return $authUser->can('View:PertemuanKelas');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PertemuanKelas');
    }

    public function update(AuthUser $authUser, PertemuanKelas $pertemuanKelas): bool
    {
        return $authUser->can('Update:PertemuanKelas');
    }

    public function delete(AuthUser $authUser, PertemuanKelas $pertemuanKelas): bool
    {
        return $authUser->can('Delete:PertemuanKelas');
    }

    public function restore(AuthUser $authUser, PertemuanKelas $pertemuanKelas): bool
    {
        return $authUser->can('Restore:PertemuanKelas');
    }

    public function forceDelete(AuthUser $authUser, PertemuanKelas $pertemuanKelas): bool
    {
        return $authUser->can('ForceDelete:PertemuanKelas');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PertemuanKelas');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PertemuanKelas');
    }

    public function replicate(AuthUser $authUser, PertemuanKelas $pertemuanKelas): bool
    {
        return $authUser->can('Replicate:PertemuanKelas');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PertemuanKelas');
    }

}