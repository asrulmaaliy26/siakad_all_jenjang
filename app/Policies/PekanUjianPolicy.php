<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PekanUjian;
use Illuminate\Auth\Access\HandlesAuthorization;

class PekanUjianPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PekanUjian');
    }

    public function view(AuthUser $authUser, PekanUjian $pekanUjian): bool
    {
        return $authUser->can('View:PekanUjian');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PekanUjian');
    }

    public function update(AuthUser $authUser, PekanUjian $pekanUjian): bool
    {
        return $authUser->can('Update:PekanUjian');
    }

    public function delete(AuthUser $authUser, PekanUjian $pekanUjian): bool
    {
        return $authUser->can('Delete:PekanUjian');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PekanUjian');
    }

    public function restore(AuthUser $authUser, PekanUjian $pekanUjian): bool
    {
        return $authUser->can('Restore:PekanUjian');
    }

    public function forceDelete(AuthUser $authUser, PekanUjian $pekanUjian): bool
    {
        return $authUser->can('ForceDelete:PekanUjian');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PekanUjian');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PekanUjian');
    }

    public function replicate(AuthUser $authUser, PekanUjian $pekanUjian): bool
    {
        return $authUser->can('Replicate:PekanUjian');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PekanUjian');
    }

}