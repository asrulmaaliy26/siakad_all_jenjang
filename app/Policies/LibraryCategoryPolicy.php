<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LibraryCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class LibraryCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryCategory');
    }

    public function view(AuthUser $authUser, LibraryCategory $libraryCategory): bool
    {
        return $authUser->can('View:LibraryCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryCategory');
    }

    public function update(AuthUser $authUser, LibraryCategory $libraryCategory): bool
    {
        return $authUser->can('Update:LibraryCategory');
    }

    public function delete(AuthUser $authUser, LibraryCategory $libraryCategory): bool
    {
        return $authUser->can('Delete:LibraryCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryCategory');
    }

    public function restore(AuthUser $authUser, LibraryCategory $libraryCategory): bool
    {
        return $authUser->can('Restore:LibraryCategory');
    }

    public function forceDelete(AuthUser $authUser, LibraryCategory $libraryCategory): bool
    {
        return $authUser->can('ForceDelete:LibraryCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryCategory');
    }

    public function replicate(AuthUser $authUser, LibraryCategory $libraryCategory): bool
    {
        return $authUser->can('Replicate:LibraryCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryCategory');
    }

}