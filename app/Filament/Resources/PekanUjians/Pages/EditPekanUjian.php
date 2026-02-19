<?php

namespace App\Filament\Resources\PekanUjians\Pages;

use App\Filament\Resources\PekanUjians\PekanUjianResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPekanUjian extends EditRecord
{
    protected static string $resource = PekanUjianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
