<?php

namespace App\Filament\Resources\WisudaMahasiswas\Pages;

use App\Filament\Resources\WisudaMahasiswas\WisudaMahasiswaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWisudaMahasiswa extends ViewRecord
{
    protected static string $resource = WisudaMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
