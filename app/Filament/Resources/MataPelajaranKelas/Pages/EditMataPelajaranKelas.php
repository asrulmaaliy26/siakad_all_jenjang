<?php

namespace App\Filament\Resources\MataPelajaranKelas\Pages;

use App\Filament\Resources\MataPelajaranKelas\MataPelajaranKelasResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMataPelajaranKelas extends EditRecord
{
    protected static string $resource = MataPelajaranKelasResource::class;

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

