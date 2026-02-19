<?php

namespace App\Filament\Resources\AbsensiSiswas\Pages;

use App\Filament\Resources\AbsensiSiswas\AbsensiSiswaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAbsensiSiswas extends ListRecords
{
    protected static string $resource = AbsensiSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
