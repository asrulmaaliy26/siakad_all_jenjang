<?php

namespace App\Filament\Resources\SarprasPeminjamen\Pages;

use App\Filament\Resources\SarprasPeminjamen\SarprasPeminjamanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSarprasPeminjamen extends ListRecords
{
    protected static string $resource = SarprasPeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
