<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DosenData;
use Illuminate\Auth\Access\HandlesAuthorization;

class DosenDataPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DosenData');
    }

    public function view(AuthUser $authUser, DosenData $dosenData): bool
    {
        return $authUser->can('View:DosenData');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DosenData');
    }

    public function update(AuthUser $authUser, DosenData $dosenData): bool
    {
        return $authUser->can('Update:DosenData');
    }

    public function delete(AuthUser $authUser, DosenData $dosenData): bool
    {
        return $authUser->can('Delete:DosenData');
    }

    public function restore(AuthUser $authUser, DosenData $dosenData): bool
    {
        return $authUser->can('Restore:DosenData');
    }

    public function forceDelete(AuthUser $authUser, DosenData $dosenData): bool
    {
        return $authUser->can('ForceDelete:DosenData');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DosenData');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DosenData');
    }

    public function replicate(AuthUser $authUser, DosenData $dosenData): bool
    {
        return $authUser->can('Replicate:DosenData');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DosenData');
    }

}