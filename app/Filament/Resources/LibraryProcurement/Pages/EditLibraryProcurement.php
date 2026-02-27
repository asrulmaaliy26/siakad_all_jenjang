<?php

namespace App\Filament\Resources\LibraryProcurement\Pages;

use App\Filament\Resources\LibraryProcurement\LibraryProcurementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLibraryProcurement extends EditRecord
{
    protected static string $resource = LibraryProcurementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
