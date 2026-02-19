<?php

namespace App\Filament\Resources\PertemuanKelas\Pages;

use App\Filament\Resources\PertemuanKelas\PertemuanKelasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPertemuanKelas extends ListRecords
{
    protected static string $resource = PertemuanKelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
