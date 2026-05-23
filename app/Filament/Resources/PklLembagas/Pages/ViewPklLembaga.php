<?php

namespace App\Filament\Resources\PklLembagas\Pages;

use App\Filament\Resources\PklLembagas\PklLembagaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPklLembaga extends ViewRecord
{
    protected static string $resource = PklLembagaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
