<?php

namespace App\Filament\Resources\TaSkripsis\Pages;

use App\Filament\Resources\TaSkripsis\TaSkripsiResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTaSkripsi extends EditRecord
{
    protected static string $resource = TaSkripsiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
