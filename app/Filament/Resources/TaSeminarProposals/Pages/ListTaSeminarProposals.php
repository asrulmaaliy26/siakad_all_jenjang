<?php

namespace App\Filament\Resources\TaSeminarProposals\Pages;

use App\Filament\Resources\TaSeminarProposals\TaSeminarProposalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaSeminarProposals extends ListRecords
{
    protected static string $resource = TaSeminarProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
