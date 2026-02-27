<?php

namespace App\Filament\Resources\ReferalCodes\Pages;

use App\Filament\Resources\ReferalCodes\ReferalCodeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditReferalCode extends EditRecord
{
    protected static string $resource = ReferalCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

