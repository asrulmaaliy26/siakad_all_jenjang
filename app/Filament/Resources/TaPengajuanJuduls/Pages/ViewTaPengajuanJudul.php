<?php

namespace App\Filament\Resources\TaPengajuanJuduls\Pages;

use App\Filament\Resources\TaPengajuanJuduls\TaPengajuanJudulResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTaPengajuanJudul extends ViewRecord
{
    protected static string $resource = TaPengajuanJudulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
