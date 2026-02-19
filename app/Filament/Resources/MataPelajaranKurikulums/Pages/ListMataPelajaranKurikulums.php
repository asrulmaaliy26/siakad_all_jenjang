<?php

namespace App\Filament\Resources\MataPelajaranKurikulums\Pages;

use App\Filament\Resources\MataPelajaranKurikulums\MataPelajaranKurikulumResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMataPelajaranKurikulums extends ListRecords
{
    protected static string $resource = MataPelajaranKurikulumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
