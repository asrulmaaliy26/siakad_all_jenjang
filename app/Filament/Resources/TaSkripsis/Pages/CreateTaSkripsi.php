<?php

namespace App\Filament\Resources\TaSkripsis\Pages;

use App\Filament\Resources\TaSkripsis\TaSkripsiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTaSkripsi extends CreateRecord
{
    protected static string $resource = TaSkripsiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

