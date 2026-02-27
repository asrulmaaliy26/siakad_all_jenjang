<?php

namespace App\Filament\Resources\AbsensiSiswas\Pages;

use App\Filament\Resources\AbsensiSiswas\AbsensiSiswaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAbsensiSiswa extends CreateRecord
{
    protected static string $resource = AbsensiSiswaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

