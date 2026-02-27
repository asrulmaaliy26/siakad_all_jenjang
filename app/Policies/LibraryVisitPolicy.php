<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LibraryVisit;
use Illuminate\Auth\Access\HandlesAuthorization;

class LibraryVisitPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryVisit');
    }

    public function view(AuthUser $authUser, LibraryVisit $libraryVisit): bool
    {
        return $authUser->can('View:LibraryVisit');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryVisit');
    }

    public function update(AuthUser $authUser, LibraryVisit $libraryVisit): bool
    {
        return $authUser->can('Update:LibraryVisit');
    }

    public function delete(AuthUser $authUser, LibraryVisit $libraryVisit): bool
    {
        return $authUser->can('Delete:LibraryVisit');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryVisit');
    }

    public function restore(AuthUser $authUser, LibraryVisit $libraryVisit): bool
    {
        return $authUser->can('Restore:LibraryVisit');
    }

    public function forceDelete(AuthUser $authUser, LibraryVisit $libraryVisit): bool
    {
        return $authUser->can('ForceDelete:LibraryVisit');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryVisit');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryVisit');
    }

    public function replicate(AuthUser $authUser, LibraryVisit $libraryVisit): bool
    {
        return $authUser->can('Replicate:LibraryVisit');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryVisit');
    }

}