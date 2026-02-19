<?php

namespace App\Filament\Resources\RiwayatPendidikans\Pages;

use App\Filament\Resources\RiwayatPendidikans\RiwayatPendidikanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRiwayatPendidikan extends EditRecord
{
    protected static string $resource = RiwayatPendidikanResource::class;

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
