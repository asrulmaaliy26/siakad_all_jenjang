<?php

namespace App\Filament\Resources\JenjangPendidikans\Pages;

use App\Filament\Resources\JenjangPendidikans\JenjangPendidikanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJenjangPendidikan extends EditRecord
{
    protected static string $resource = JenjangPendidikanResource::class;

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
