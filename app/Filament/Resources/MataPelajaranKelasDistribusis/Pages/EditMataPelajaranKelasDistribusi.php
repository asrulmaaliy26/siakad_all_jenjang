<?php

namespace App\Filament\Resources\MataPelajaranKelasDistribusis\Pages;

use App\Filament\Resources\MataPelajaranKelasDistribusis\MataPelajaranKelasDistribusiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMataPelajaranKelasDistribusi extends EditRecord
{
    protected static string $resource = MataPelajaranKelasDistribusiResource::class;

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
