<?php

namespace App\Filament\Resources\ReferenceOptions\Pages;

use App\Filament\Resources\ReferenceOptions\ReferenceOptionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReferenceOption extends CreateRecord
{
    protected static string $resource = ReferenceOptionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // kembali ke list page
    }
}
