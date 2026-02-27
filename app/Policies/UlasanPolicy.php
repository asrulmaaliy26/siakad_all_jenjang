<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Ulasan;
use Illuminate\Auth\Access\HandlesAuthorization;

class UlasanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Ulasan');
    }

    public function view(AuthUser $authUser, Ulasan $ulasan): bool
    {
        return $authUser->can('View:Ulasan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Ulasan');
    }

    public function update(AuthUser $authUser, Ulasan $ulasan): bool
    {
        return $authUser->can('Update:Ulasan');
    }

    public function delete(AuthUser $authUser, Ulasan $ulasan): bool
    {
        return $authUser->can('Delete:Ulasan');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Ulasan');
    }

    public function restore(AuthUser $authUser, Ulasan $ulasan): bool
    {
        return $authUser->can('Restore:Ulasan');
    }

    public function forceDelete(AuthUser $authUser, Ulasan $ulasan): bool
    {
        return $authUser->can('ForceDelete:Ulasan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Ulasan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Ulasan');
    }

    public function replicate(AuthUser $authUser, Ulasan $ulasan): bool
    {
        return $authUser->can('Replicate:Ulasan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Ulasan');
    }

}