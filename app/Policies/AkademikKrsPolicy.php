<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AkademikKrs;
use Illuminate\Auth\Access\HandlesAuthorization;

class AkademikKrsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AkademikKrs');
    }

    public function view(AuthUser $authUser, AkademikKrs $akademikKrs): bool
    {
        return $authUser->can('View:AkademikKrs');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AkademikKrs');
    }

    public function update(AuthUser $authUser, AkademikKrs $akademikKrs): bool
    {
        return $authUser->can('Update:AkademikKrs');
    }

    public function delete(AuthUser $authUser, AkademikKrs $akademikKrs): bool
    {
        return $authUser->can('Delete:AkademikKrs');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AkademikKrs');
    }

    public function restore(AuthUser $authUser, AkademikKrs $akademikKrs): bool
    {
        return $authUser->can('Restore:AkademikKrs');
    }

    public function forceDelete(AuthUser $authUser, AkademikKrs $akademikKrs): bool
    {
        return $authUser->can('ForceDelete:AkademikKrs');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AkademikKrs');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AkademikKrs');
    }

    public function replicate(AuthUser $authUser, AkademikKrs $akademikKrs): bool
    {
        return $authUser->can('Replicate:AkademikKrs');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AkademikKrs');
    }

}