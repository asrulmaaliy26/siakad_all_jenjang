<?php

namespace App\Filament\Resources\TaPengajuanJuduls\Pages;

use App\Filament\Resources\TaPengajuanJuduls\TaPengajuanJudulResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaPengajuanJuduls extends ListRecords
{
    protected static string $resource = TaPengajuanJudulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
