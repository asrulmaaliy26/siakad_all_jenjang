<?php

namespace App\Filament\Resources\SiswaDataOrangTuas\Pages;

use App\Filament\Resources\SiswaDataOrangTuas\SiswaDataOrangTuaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSiswaDataOrangTua extends EditRecord
{
    protected static string $resource = SiswaDataOrangTuaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
