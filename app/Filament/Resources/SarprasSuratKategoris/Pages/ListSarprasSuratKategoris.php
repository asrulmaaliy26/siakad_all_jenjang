<?php

namespace App\Filament\Resources\SarprasSuratKategoris\Pages;

use App\Filament\Resources\SarprasSuratKategoris\SarprasSuratKategoriResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSarprasSuratKategoris extends ListRecords
{
    protected static string $resource = SarprasSuratKategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
