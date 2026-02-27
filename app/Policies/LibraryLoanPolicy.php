<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LibraryLoan;
use Illuminate\Auth\Access\HandlesAuthorization;

class LibraryLoanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LibraryLoan');
    }

    public function view(AuthUser $authUser, LibraryLoan $libraryLoan): bool
    {
        return $authUser->can('View:LibraryLoan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LibraryLoan');
    }

    public function update(AuthUser $authUser, LibraryLoan $libraryLoan): bool
    {
        return $authUser->can('Update:LibraryLoan');
    }

    public function delete(AuthUser $authUser, LibraryLoan $libraryLoan): bool
    {
        return $authUser->can('Delete:LibraryLoan');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:LibraryLoan');
    }

    public function restore(AuthUser $authUser, LibraryLoan $libraryLoan): bool
    {
        return $authUser->can('Restore:LibraryLoan');
    }

    public function forceDelete(AuthUser $authUser, LibraryLoan $libraryLoan): bool
    {
        return $authUser->can('ForceDelete:LibraryLoan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LibraryLoan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LibraryLoan');
    }

    public function replicate(AuthUser $authUser, LibraryLoan $libraryLoan): bool
    {
        return $authUser->can('Replicate:LibraryLoan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LibraryLoan');
    }

}