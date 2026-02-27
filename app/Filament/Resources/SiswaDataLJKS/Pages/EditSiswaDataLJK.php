<?php

namespace App\Filament\Resources\SiswaDataLJKS\Pages;

use App\Filament\Resources\SiswaDataLJKS\SiswaDataLJKResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSiswaDataLJK extends EditRecord
{
    protected static string $resource = SiswaDataLJKResource::class;

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

