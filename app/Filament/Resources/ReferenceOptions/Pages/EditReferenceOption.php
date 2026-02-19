<?php

namespace App\Filament\Resources\ReferenceOptions\Pages;

use App\Filament\Resources\ReferenceOptions\ReferenceOptionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReferenceOption extends EditRecord
{
    protected static string $resource = ReferenceOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // kembali ke list page
    }
}
