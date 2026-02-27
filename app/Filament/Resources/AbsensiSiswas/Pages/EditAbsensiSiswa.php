<?php

namespace App\Filament\Resources\AbsensiSiswas\Pages;

use App\Filament\Resources\AbsensiSiswas\AbsensiSiswaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAbsensiSiswa extends EditRecord
{
    protected static string $resource = AbsensiSiswaResource::class;

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

