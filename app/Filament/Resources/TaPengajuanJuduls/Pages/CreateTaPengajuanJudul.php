<?php

namespace App\Filament\Resources\TaPengajuanJuduls\Pages;

use App\Filament\Resources\TaPengajuanJuduls\TaPengajuanJudulResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTaPengajuanJudul extends CreateRecord
{
    protected static string $resource = TaPengajuanJudulResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

