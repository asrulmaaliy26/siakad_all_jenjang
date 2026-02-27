<?php

namespace App\Filament\Resources\PeriodeWisudas\Pages;

use App\Filament\Resources\PeriodeWisudas\PeriodeWisudaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPeriodeWisuda extends ViewRecord
{
    protected static string $resource = PeriodeWisudaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
