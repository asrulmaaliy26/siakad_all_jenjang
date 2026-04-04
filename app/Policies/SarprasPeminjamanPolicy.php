<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SarprasPeminjaman;
use Illuminate\Auth\Access\HandlesAuthorization;

class SarprasPeminjamanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SarprasPeminjaman');
    }

    public function view(AuthUser $authUser, SarprasPeminjaman $sarprasPeminjaman): bool
    {
        return $authUser->can('View:SarprasPeminjaman');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SarprasPeminjaman');
    }

    public function update(AuthUser $authUser, SarprasPeminjaman $sarprasPeminjaman): bool
    {
        return $authUser->can('Update:SarprasPeminjaman');
    }

    public function delete(AuthUser $authUser, SarprasPeminjaman $sarprasPeminjaman): bool
    {
        return $authUser->can('Delete:SarprasPeminjaman');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SarprasPeminjaman');
    }

    public function restore(AuthUser $authUser, SarprasPeminjaman $sarprasPeminjaman): bool
    {
        return $authUser->can('Restore:SarprasPeminjaman');
    }

    public function forceDelete(AuthUser $authUser, SarprasPeminjaman $sarprasPeminjaman): bool
    {
        return $authUser->can('ForceDelete:SarprasPeminjaman');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SarprasPeminjaman');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SarprasPeminjaman');
    }

    public function replicate(AuthUser $authUser, SarprasPeminjaman $sarprasPeminjaman): bool
    {
        return $authUser->can('Replicate:SarprasPeminjaman');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SarprasPeminjaman');
    }

}