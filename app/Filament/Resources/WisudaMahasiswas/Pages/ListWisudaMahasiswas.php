<?php

namespace App\Filament\Resources\WisudaMahasiswas\Pages;

use App\Filament\Resources\WisudaMahasiswas\WisudaMahasiswaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWisudaMahasiswas extends ListRecords
{
    protected static string $resource = WisudaMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
