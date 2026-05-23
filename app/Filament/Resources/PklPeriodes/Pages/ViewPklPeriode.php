<?php

namespace App\Filament\Resources\PklPeriodes\Pages;

use App\Filament\Resources\PklPeriodes\PklPeriodeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPklPeriode extends ViewRecord
{
    protected static string $resource = PklPeriodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
