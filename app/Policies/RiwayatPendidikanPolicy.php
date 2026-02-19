<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RiwayatPendidikan;
use Illuminate\Auth\Access\HandlesAuthorization;

class RiwayatPendidikanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RiwayatPendidikan');
    }

    public function view(AuthUser $authUser, RiwayatPendidikan $riwayatPendidikan): bool
    {
        return $authUser->can('View:RiwayatPendidikan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RiwayatPendidikan');
    }

    public function update(AuthUser $authUser, RiwayatPendidikan $riwayatPendidikan): bool
    {
        return $authUser->can('Update:RiwayatPendidikan');
    }

    public function delete(AuthUser $authUser, RiwayatPendidikan $riwayatPendidikan): bool
    {
        return $authUser->can('Delete:RiwayatPendidikan');
    }

    public function restore(AuthUser $authUser, RiwayatPendidikan $riwayatPendidikan): bool
    {
        return $authUser->can('Restore:RiwayatPendidikan');
    }

    public function forceDelete(AuthUser $authUser, RiwayatPendidikan $riwayatPendidikan): bool
    {
        return $authUser->can('ForceDelete:RiwayatPendidikan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RiwayatPendidikan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RiwayatPendidikan');
    }

    public function replicate(AuthUser $authUser, RiwayatPendidikan $riwayatPendidikan): bool
    {
        return $authUser->can('Replicate:RiwayatPendidikan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RiwayatPendidikan');
    }

}