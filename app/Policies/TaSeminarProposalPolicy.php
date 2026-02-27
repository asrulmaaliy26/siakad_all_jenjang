<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TaSeminarProposal;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaSeminarProposalPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TaSeminarProposal');
    }

    public function view(AuthUser $authUser, TaSeminarProposal $taSeminarProposal): bool
    {
        return $authUser->can('View:TaSeminarProposal');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TaSeminarProposal');
    }

    public function update(AuthUser $authUser, TaSeminarProposal $taSeminarProposal): bool
    {
        return $authUser->can('Update:TaSeminarProposal');
    }

    public function delete(AuthUser $authUser, TaSeminarProposal $taSeminarProposal): bool
    {
        return $authUser->can('Delete:TaSeminarProposal');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TaSeminarProposal');
    }

    public function restore(AuthUser $authUser, TaSeminarProposal $taSeminarProposal): bool
    {
        return $authUser->can('Restore:TaSeminarProposal');
    }

    public function forceDelete(AuthUser $authUser, TaSeminarProposal $taSeminarProposal): bool
    {
        return $authUser->can('ForceDelete:TaSeminarProposal');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TaSeminarProposal');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TaSeminarProposal');
    }

    public function replicate(AuthUser $authUser, TaSeminarProposal $taSeminarProposal): bool
    {
        return $authUser->can('Replicate:TaSeminarProposal');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TaSeminarProposal');
    }

}