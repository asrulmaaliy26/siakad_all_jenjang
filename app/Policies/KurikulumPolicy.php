<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Kurikulum;
use Illuminate\Auth\Access\HandlesAuthorization;

class KurikulumPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Kurikulum');
    }

    public function view(AuthUser $authUser, Kurikulum $kurikulum): bool
    {
        return $authUser->can('View:Kurikulum');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Kurikulum');
    }

    public function update(AuthUser $authUser, Kurikulum $kurikulum): bool
    {
        return $authUser->can('Update:Kurikulum');
    }

    public function delete(AuthUser $authUser, Kurikulum $kurikulum): bool
    {
        return $authUser->can('Delete:Kurikulum');
    }

    public function restore(AuthUser $authUser, Kurikulum $kurikulum): bool
    {
        return $authUser->can('Restore:Kurikulum');
    }

    public function forceDelete(AuthUser $authUser, Kurikulum $kurikulum): bool
    {
        return $authUser->can('ForceDelete:Kurikulum');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Kurikulum');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Kurikulum');
    }

    public function replicate(AuthUser $authUser, Kurikulum $kurikulum): bool
    {
        return $authUser->can('Replicate:Kurikulum');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Kurikulum');
    }

}