<?php

namespace App\Filament\Resources\JenjangPendidikans\Pages;

use App\Filament\Resources\JenjangPendidikans\JenjangPendidikanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJenjangPendidikan extends CreateRecord
{
    protected static string $resource = JenjangPendidikanResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // kembali ke list page
    }
}
