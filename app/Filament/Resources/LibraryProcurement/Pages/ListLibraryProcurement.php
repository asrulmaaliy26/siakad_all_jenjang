<?php

namespace App\Filament\Resources\LibraryProcurement\Pages;

use App\Filament\Resources\LibraryProcurement\LibraryProcurementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibraryProcurement extends ListRecords
{
    protected static string $resource = LibraryProcurementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
