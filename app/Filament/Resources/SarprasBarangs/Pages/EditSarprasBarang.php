<?php

namespace App\Filament\Resources\SarprasBarangs\Pages;

use App\Filament\Resources\SarprasBarangs\SarprasBarangResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSarprasBarang extends EditRecord
{
    protected static string $resource = SarprasBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
