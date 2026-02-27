<?php

namespace App\Filament\Resources\PertemuanKelas\Pages;

use App\Filament\Resources\PertemuanKelas\PertemuanKelasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPertemuanKelas extends EditRecord
{
    protected static string $resource = PertemuanKelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

