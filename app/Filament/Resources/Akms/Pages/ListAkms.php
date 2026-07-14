<?php

namespace App\Filament\Resources\Akms\Pages;

use App\Filament\Resources\Akms\AkmResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAkms extends ListRecords
{
    protected static string $resource = AkmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
