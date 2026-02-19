<?php

namespace App\Filament\Resources\ReferenceOptions\Pages;

use App\Filament\Resources\ReferenceOptions\ReferenceOptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReferenceOptions extends ListRecords
{
    protected static string $resource = ReferenceOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
