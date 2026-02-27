<?php

namespace App\Filament\Resources\ReferalCodes\Pages;

use App\Filament\Resources\ReferalCodes\ReferalCodeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReferalCode extends ViewRecord
{
    protected static string $resource = ReferalCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
