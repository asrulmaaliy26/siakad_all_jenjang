<?php

namespace App\Filament\Resources\ReferalCodes\Pages;

use App\Filament\Resources\ReferalCodes\ReferalCodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReferalCodes extends ListRecords
{
    protected static string $resource = ReferalCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
