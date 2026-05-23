<?php

namespace App\Filament\Resources\PklPendaftarans\Pages;

use App\Filament\Resources\PklPendaftarans\PklPendaftaranResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPklPendaftaran extends EditRecord
{
    protected static string $resource = PklPendaftaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
