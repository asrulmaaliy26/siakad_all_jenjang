<?php

namespace App\Filament\Resources\RiwayatPendidikans\Pages;

use App\Filament\Resources\RiwayatPendidikans\RiwayatPendidikanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRiwayatPendidikan extends CreateRecord
{
    protected static string $resource = RiwayatPendidikanResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // kembali ke list page
    }
}
