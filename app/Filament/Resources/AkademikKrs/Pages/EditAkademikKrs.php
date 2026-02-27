<?php

namespace App\Filament\Resources\AkademikKrs\Pages;

use App\Filament\Resources\AkademikKrs\AkademikKrsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAkademikKrs extends EditRecord
{
    protected static string $resource = AkademikKrsResource::class;

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

