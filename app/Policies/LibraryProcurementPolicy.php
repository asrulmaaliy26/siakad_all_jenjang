<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LibraryProcurement;
use Illuminate\Auth\Access\HandlesAuthorization;

class LibraryProcurementPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryProcurement');
    }

    public function view(AuthUser $authUser, LibraryProcurement $libraryProcurement): bool
    {
        return $authUser->can('View:LibraryProcurement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryProcurement');
    }

    public function update(AuthUser $authUser, LibraryProcurement $libraryProcurement): bool
    {
        return $authUser->can('Update:LibraryProcurement');
    }

    public function delete(AuthUser $authUser, LibraryProcurement $libraryProcurement): bool
    {
        return $authUser->can('Delete:LibraryProcurement');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryProcurement');
    }

    public function restore(AuthUser $authUser, LibraryProcurement $libraryProcurement): bool
    {
        return $authUser->can('Restore:LibraryProcurement');
    }

    public function forceDelete(AuthUser $authUser, LibraryProcurement $libraryProcurement): bool
    {
        return $authUser->can('ForceDelete:LibraryProcurement');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryProcurement');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryProcurement');
    }

    public function replicate(AuthUser $authUser, LibraryProcurement $libraryProcurement): bool
    {
        return $authUser->can('Replicate:LibraryProcurement');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryProcurement');
    }

}