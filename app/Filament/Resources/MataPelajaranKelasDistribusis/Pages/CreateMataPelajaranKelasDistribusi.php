<?php

namespace App\Filament\Resources\MataPelajaranKelasDistribusis\Pages;

use App\Filament\Resources\MataPelajaranKelasDistribusis\MataPelajaranKelasDistribusiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMataPelajaranKelasDistribusi extends CreateRecord
{
    protected static string $resource = MataPelajaranKelasDistribusiResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // kembali ke list page
    }
}
