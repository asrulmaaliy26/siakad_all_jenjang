<?php

namespace App\Filament\Resources\MataPelajaranMasters\Pages;

use App\Filament\Resources\MataPelajaranMasters\MataPelajaranMasterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMataPelajaranMaster extends EditRecord
{
    protected static string $resource = MataPelajaranMasterResource::class;

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

