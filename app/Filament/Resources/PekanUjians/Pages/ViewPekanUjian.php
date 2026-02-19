<?php

namespace App\Filament\Resources\PekanUjians\Pages;

use App\Filament\Resources\PekanUjians\PekanUjianResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPekanUjian extends ViewRecord
{
    protected static string $resource = PekanUjianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
