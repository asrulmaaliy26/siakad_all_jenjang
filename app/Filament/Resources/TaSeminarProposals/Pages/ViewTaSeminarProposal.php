<?php

namespace App\Filament\Resources\TaSeminarProposals\Pages;

use App\Filament\Resources\TaSeminarProposals\TaSeminarProposalResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTaSeminarProposal extends ViewRecord
{
    protected static string $resource = TaSeminarProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
