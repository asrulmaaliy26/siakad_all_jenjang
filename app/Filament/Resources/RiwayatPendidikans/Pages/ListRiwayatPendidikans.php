<?php

namespace App\Filament\Resources\RiwayatPendidikans\Pages;

use App\Filament\Resources\RiwayatPendidikans\RiwayatPendidikanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRiwayatPendidikans extends ListRecords
{
    protected static string $resource = RiwayatPendidikanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
