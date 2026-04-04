<?php

namespace App\Filament\Resources\SarprasSuratKeluars\Pages;

use App\Filament\Resources\SarprasSuratKeluars\SarprasSuratKeluarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSarprasSuratKeluar extends EditRecord
{
    protected static string $resource = SarprasSuratKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
