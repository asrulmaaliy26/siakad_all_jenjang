<?php

namespace App\Filament\Resources\LibraryBook\Pages;

use App\Filament\Resources\LibraryBook\LibraryBookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLibraryBook extends EditRecord
{
    protected static string $resource = LibraryBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
