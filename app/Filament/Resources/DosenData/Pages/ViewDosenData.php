<?php

namespace App\Filament\Resources\DosenData\Pages;

use App\Filament\Resources\DosenData\DosenDataResource;
// use App\Models\DosenData;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDosenData extends ViewRecord
{
    protected static string $resource = DosenDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
