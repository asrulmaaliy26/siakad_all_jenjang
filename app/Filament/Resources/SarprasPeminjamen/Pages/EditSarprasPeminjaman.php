<?php

namespace App\Filament\Resources\SarprasPeminjamen\Pages;

use App\Filament\Resources\SarprasPeminjamen\SarprasPeminjamanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSarprasPeminjaman extends EditRecord
{
    protected static string $resource = SarprasPeminjamanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
