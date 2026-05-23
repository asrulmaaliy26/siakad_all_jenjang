<?php

namespace App\Filament\Resources\PklLembagas\Pages;

use App\Filament\Resources\PklLembagas\PklLembagaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPklLembaga extends EditRecord
{
    protected static string $resource = PklLembagaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
