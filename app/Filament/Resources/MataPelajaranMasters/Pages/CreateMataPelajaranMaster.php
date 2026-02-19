<?php

namespace App\Filament\Resources\MataPelajaranMasters\Pages;

use App\Filament\Resources\MataPelajaranMasters\MataPelajaranMasterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMataPelajaranMaster extends CreateRecord
{
    protected static string $resource = MataPelajaranMasterResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // kembali ke list page
    }
}
