<?php

namespace App\Filament\Resources\SiswaDataLJKS\Pages;

use App\Filament\Resources\SiswaDataLJKS\SiswaDataLJKResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSiswaDataLJKS extends ListRecords
{
    protected static string $resource = SiswaDataLJKResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
