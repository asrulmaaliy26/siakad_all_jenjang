<?php

namespace App\Filament\Resources\SiswaDataPendaftars\Pages;

use App\Filament\Resources\SiswaDataPendaftars\SiswaDataPendaftarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSiswaDataPendaftar extends EditRecord
{
    protected static string $resource = SiswaDataPendaftarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

