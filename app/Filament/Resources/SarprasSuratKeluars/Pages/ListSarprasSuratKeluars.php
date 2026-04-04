<?php

namespace App\Filament\Resources\SarprasSuratKeluars\Pages;

use App\Filament\Resources\SarprasSuratKeluars\SarprasSuratKeluarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSarprasSuratKeluars extends ListRecords
{
    protected static string $resource = SarprasSuratKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
