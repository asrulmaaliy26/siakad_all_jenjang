<?php

namespace App\Filament\Resources\ReferalCodes\Pages;

use App\Filament\Resources\ReferalCodes\ReferalCodeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReferalCode extends CreateRecord
{
    protected static string $resource = ReferalCodeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

