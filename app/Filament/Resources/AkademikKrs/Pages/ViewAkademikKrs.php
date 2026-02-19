<?php

namespace App\Filament\Resources\AkademikKrs\Pages;

use App\Filament\Resources\AkademikKrs\AkademikKrsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAkademikKrs extends ViewRecord
{
    protected static string $resource = AkademikKrsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
