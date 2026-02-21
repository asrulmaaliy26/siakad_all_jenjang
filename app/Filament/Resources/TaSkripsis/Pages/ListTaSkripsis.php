<?php

namespace App\Filament\Resources\TaSkripsis\Pages;

use App\Filament\Resources\TaSkripsis\TaSkripsiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaSkripsis extends ListRecords
{
    protected static string $resource = TaSkripsiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
