<?php

namespace App\Filament\Resources\PeriodeWisudas\Pages;

use App\Filament\Resources\PeriodeWisudas\PeriodeWisudaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPeriodeWisuda extends EditRecord
{
    protected static string $resource = PeriodeWisudaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
