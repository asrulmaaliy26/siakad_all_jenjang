<?php

namespace App\Filament\Resources\AkademikKrs\Pages;

use App\Filament\Resources\AkademikKrs\AkademikKrsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAkademikKrs extends CreateRecord
{
    protected static string $resource = AkademikKrsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

