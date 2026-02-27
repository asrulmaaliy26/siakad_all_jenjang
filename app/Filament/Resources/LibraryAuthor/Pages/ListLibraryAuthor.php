<?php

namespace App\Filament\Resources\LibraryAuthor\Pages;

use App\Filament\Resources\LibraryAuthor\LibraryAuthorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibraryAuthor extends ListRecords
{
    protected static string $resource = LibraryAuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
