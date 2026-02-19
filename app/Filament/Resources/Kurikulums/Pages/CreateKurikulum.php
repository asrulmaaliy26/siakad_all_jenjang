<?php

namespace App\Filament\Resources\Kurikulums\Pages;

use App\Filament\Resources\Kurikulums\KurikulumResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKurikulum extends CreateRecord
{
    protected static string $resource = KurikulumResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // kembali ke list page
    }
}
