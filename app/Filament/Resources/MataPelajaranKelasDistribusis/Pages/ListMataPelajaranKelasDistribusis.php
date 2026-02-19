<?php

namespace App\Filament\Resources\MataPelajaranKelasDistribusis\Pages;

use App\Filament\Resources\MataPelajaranKelasDistribusis\MataPelajaranKelasDistribusiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMataPelajaranKelasDistribusis extends ListRecords
{
    protected static string $resource = MataPelajaranKelasDistribusiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
