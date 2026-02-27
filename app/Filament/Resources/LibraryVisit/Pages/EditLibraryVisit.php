<?php

namespace App\Filament\Resources\LibraryVisit\Pages;

use App\Filament\Resources\LibraryVisit\LibraryVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLibraryVisit extends EditRecord
{
    protected static string $resource = LibraryVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
