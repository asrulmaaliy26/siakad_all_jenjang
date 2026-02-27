<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TaPengajuanJudul;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaPengajuanJudulPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TaPengajuanJudul');
    }

    public function view(AuthUser $authUser, TaPengajuanJudul $taPengajuanJudul): bool
    {
        return $authUser->can('View:TaPengajuanJudul');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TaPengajuanJudul');
    }

    public function update(AuthUser $authUser, TaPengajuanJudul $taPengajuanJudul): bool
    {
        return $authUser->can('Update:TaPengajuanJudul');
    }

    public function delete(AuthUser $authUser, TaPengajuanJudul $taPengajuanJudul): bool
    {
        return $authUser->can('Delete:TaPengajuanJudul');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TaPengajuanJudul');
    }

    public function restore(AuthUser $authUser, TaPengajuanJudul $taPengajuanJudul): bool
    {
        return $authUser->can('Restore:TaPengajuanJudul');
    }

    public function forceDelete(AuthUser $authUser, TaPengajuanJudul $taPengajuanJudul): bool
    {
        return $authUser->can('ForceDelete:TaPengajuanJudul');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TaPengajuanJudul');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TaPengajuanJudul');
    }

    public function replicate(AuthUser $authUser, TaPengajuanJudul $taPengajuanJudul): bool
    {
        return $authUser->can('Replicate:TaPengajuanJudul');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TaPengajuanJudul');
    }

}