<?php

namespace App\Filament\Resources\SarprasKategoris\Pages;

use App\Filament\Resources\SarprasKategoris\SarprasKategoriResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSarprasKategori extends EditRecord
{
    protected static string $resource = SarprasKategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
