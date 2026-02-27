<?php

namespace App\Filament\Resources\TaSeminarProposals\Pages;

use App\Filament\Resources\TaSeminarProposals\TaSeminarProposalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTaSeminarProposal extends CreateRecord
{
    protected static string $resource = TaSeminarProposalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

