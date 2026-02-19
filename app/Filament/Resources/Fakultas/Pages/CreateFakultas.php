<?php

namespace App\Filament\Resources\Fakultas\Pages;

use App\Filament\Resources\Fakultas\FakultasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFakultas extends CreateRecord
{
    protected static string $resource = FakultasResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // kembali ke list page
    }
}
