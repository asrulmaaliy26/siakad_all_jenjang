<?php

namespace App\Filament\Resources\LibraryCategory\Pages;

use App\Filament\Resources\LibraryCategory\LibraryCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibraryCategory extends ListRecords
{
    protected static string $resource = LibraryCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
