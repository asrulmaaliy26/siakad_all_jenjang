<?php

namespace App\Filament\Resources\PklPeriodes\Pages;

use App\Filament\Resources\PklPeriodes\PklPeriodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPklPeriodes extends ListRecords
{
    protected static string $resource = PklPeriodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
