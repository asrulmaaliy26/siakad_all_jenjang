<?php

namespace App\Filament\Resources\WisudaMahasiswas\Pages;

use App\Filament\Resources\WisudaMahasiswas\WisudaMahasiswaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditWisudaMahasiswa extends EditRecord
{
    protected static string $resource = WisudaMahasiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
