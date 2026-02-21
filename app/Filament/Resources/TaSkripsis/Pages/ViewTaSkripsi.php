<?php

namespace App\Filament\Resources\TaSkripsis\Pages;

use App\Filament\Resources\TaSkripsis\TaSkripsiResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTaSkripsi extends ViewRecord
{
    protected static string $resource = TaSkripsiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
