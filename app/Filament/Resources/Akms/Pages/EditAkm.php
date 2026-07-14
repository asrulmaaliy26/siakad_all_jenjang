<?php

namespace App\Filament\Resources\Akms\Pages;

use App\Filament\Resources\Akms\AkmResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAkm extends EditRecord
{
    protected static string $resource = AkmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
