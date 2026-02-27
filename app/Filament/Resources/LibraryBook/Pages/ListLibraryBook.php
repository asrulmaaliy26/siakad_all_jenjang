<?php

namespace App\Filament\Resources\LibraryBook\Pages;

use App\Filament\Resources\LibraryBook\LibraryBookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibraryBook extends ListRecords
{
    protected static string $resource = LibraryBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
