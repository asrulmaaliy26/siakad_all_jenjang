<?php

namespace App\Filament\Resources\PklLembagas\Pages;

use App\Filament\Resources\PklLembagas\PklLembagaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPklLembagas extends ListRecords
{
    protected static string $resource = PklLembagaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
