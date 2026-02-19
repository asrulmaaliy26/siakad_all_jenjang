<?php

namespace App\Filament\Resources\PekanUjians\Pages;

use App\Filament\Resources\PekanUjians\PekanUjianResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPekanUjians extends ListRecords
{
    protected static string $resource = PekanUjianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
