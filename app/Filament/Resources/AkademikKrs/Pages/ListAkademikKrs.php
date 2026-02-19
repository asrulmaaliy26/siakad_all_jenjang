<?php

namespace App\Filament\Resources\AkademikKrs\Pages;

use App\Filament\Resources\AkademikKrs\AkademikKrsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAkademikKrs extends ListRecords
{
    protected static string $resource = AkademikKrsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
