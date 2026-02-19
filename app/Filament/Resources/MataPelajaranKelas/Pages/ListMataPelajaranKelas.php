<?php

namespace App\Filament\Resources\MataPelajaranKelas\Pages;

use App\Filament\Resources\MataPelajaranKelas\MataPelajaranKelasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMataPelajaranKelas extends ListRecords
{
    protected static string $resource = MataPelajaranKelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
