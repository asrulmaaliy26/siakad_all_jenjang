<?php

namespace App\Filament\Resources\MataPelajaranMasters\Pages;

use App\Filament\Resources\MataPelajaranMasters\MataPelajaranMasterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMataPelajaranMasters extends ListRecords
{
    protected static string $resource = MataPelajaranMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
