<?php

namespace App\Filament\Resources\Akms\Pages;

use App\Filament\Resources\Akms\AkmResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAkm extends ViewRecord
{
    protected static string $resource = AkmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
