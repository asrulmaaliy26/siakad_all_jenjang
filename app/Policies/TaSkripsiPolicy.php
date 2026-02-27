<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TaSkripsi;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaSkripsiPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TaSkripsi');
    }

    public function view(AuthUser $authUser, TaSkripsi $taSkripsi): bool
    {
        return $authUser->can('View:TaSkripsi');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TaSkripsi');
    }

    public function update(AuthUser $authUser, TaSkripsi $taSkripsi): bool
    {
        return $authUser->can('Update:TaSkripsi');
    }

    public function delete(AuthUser $authUser, TaSkripsi $taSkripsi): bool
    {
        return $authUser->can('Delete:TaSkripsi');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TaSkripsi');
    }

    public function restore(AuthUser $authUser, TaSkripsi $taSkripsi): bool
    {
        return $authUser->can('Restore:TaSkripsi');
    }

    public function forceDelete(AuthUser $authUser, TaSkripsi $taSkripsi): bool
    {
        return $authUser->can('ForceDelete:TaSkripsi');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TaSkripsi');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TaSkripsi');
    }

    public function replicate(AuthUser $authUser, TaSkripsi $taSkripsi): bool
    {
        return $authUser->can('Replicate:TaSkripsi');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TaSkripsi');
    }

}