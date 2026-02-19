<?php

namespace App\Filament\Resources\MataPelajaranKelas\Pages;

use App\Filament\Resources\MataPelajaranKelas\MataPelajaranKelasResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMataPelajaranKelas extends ViewRecord
{
    protected static string $resource = MataPelajaranKelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
