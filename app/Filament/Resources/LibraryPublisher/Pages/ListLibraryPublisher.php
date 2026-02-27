<?php

namespace App\Filament\Resources\LibraryPublisher\Pages;

use App\Filament\Resources\LibraryPublisher\LibraryPublisherResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibraryPublisher extends ListRecords
{
    protected static string $resource = LibraryPublisherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
