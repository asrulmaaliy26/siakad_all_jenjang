<?php

namespace App\Filament\Resources\PklPendaftarans\Pages;

use App\Filament\Resources\PklPendaftarans\PklPendaftaranResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPklPendaftaran extends ViewRecord
{
    protected static string $resource = PklPendaftaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
