<?php

namespace App\Filament\Resources\RiwayatPendidikans\Pages;

use App\Filament\Resources\RiwayatPendidikans\RiwayatPendidikanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class VIewRiwayatPendidikan extends ViewRecord
{
    protected static string $resource = RiwayatPendidikanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
