<?php

namespace App\Filament\Resources\SarprasSuratKategoris\Pages;

use App\Filament\Resources\SarprasSuratKategoris\SarprasSuratKategoriResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSarprasSuratKategori extends EditRecord
{
    protected static string $resource = SarprasSuratKategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
