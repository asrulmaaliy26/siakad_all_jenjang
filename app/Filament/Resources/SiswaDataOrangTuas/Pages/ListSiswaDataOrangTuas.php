<?php

namespace App\Filament\Resources\SiswaDataOrangTuas\Pages;

use App\Filament\Resources\SiswaDataOrangTuas\SiswaDataOrangTuaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSiswaDataOrangTuas extends ListRecords
{
    protected static string $resource = SiswaDataOrangTuaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
