<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\JenjangPendidikan;
use Illuminate\Auth\Access\HandlesAuthorization;

class JenjangPendidikanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:JenjangPendidikan');
    }

    public function view(AuthUser $authUser, JenjangPendidikan $jenjangPendidikan): bool
    {
        return $authUser->can('View:JenjangPendidikan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:JenjangPendidikan');
    }

    public function update(AuthUser $authUser, JenjangPendidikan $jenjangPendidikan): bool
    {
        return $authUser->can('Update:JenjangPendidikan');
    }

    public function delete(AuthUser $authUser, JenjangPendidikan $jenjangPendidikan): bool
    {
        return $authUser->can('Delete:JenjangPendidikan');
    }

    public function restore(AuthUser $authUser, JenjangPendidikan $jenjangPendidikan): bool
    {
        return $authUser->can('Restore:JenjangPendidikan');
    }

    public function forceDelete(AuthUser $authUser, JenjangPendidikan $jenjangPendidikan): bool
    {
        return $authUser->can('ForceDelete:JenjangPendidikan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:JenjangPendidikan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:JenjangPendidikan');
    }

    public function replicate(AuthUser $authUser, JenjangPendidikan $jenjangPendidikan): bool
    {
        return $authUser->can('Replicate:JenjangPendidikan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:JenjangPendidikan');
    }

}