<?php

namespace App\Filament\Resources\PeriodeWisudas\Pages;

use App\Filament\Resources\PeriodeWisudas\PeriodeWisudaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPeriodeWisudas extends ListRecords
{
    protected static string $resource = PeriodeWisudaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
