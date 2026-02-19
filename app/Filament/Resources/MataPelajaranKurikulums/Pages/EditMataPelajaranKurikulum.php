<?php

namespace App\Filament\Resources\MataPelajaranKurikulums\Pages;

use App\Filament\Resources\MataPelajaranKurikulums\MataPelajaranKurikulumResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMataPelajaranKurikulum extends EditRecord
{
    protected static string $resource = MataPelajaranKurikulumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
