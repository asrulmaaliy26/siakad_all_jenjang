<?php

namespace App\Filament\Resources\MataPelajaranKelas\Pages;

use App\Filament\Resources\MataPelajaranKelas\MataPelajaranKelasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMataPelajaranKelas extends CreateRecord
{
    protected static string $resource = MataPelajaranKelasResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

