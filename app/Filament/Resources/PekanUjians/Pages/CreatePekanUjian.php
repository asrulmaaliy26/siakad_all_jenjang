<?php

namespace App\Filament\Resources\PekanUjians\Pages;

use App\Filament\Resources\PekanUjians\PekanUjianResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePekanUjian extends CreateRecord
{
    protected static string $resource = PekanUjianResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

