<?php

namespace App\Filament\Resources\LibraryVisit\Pages;

use App\Filament\Resources\LibraryVisit\LibraryVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibraryVisit extends ListRecords
{
    protected static string $resource = LibraryVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
