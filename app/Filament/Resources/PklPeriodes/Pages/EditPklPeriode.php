<?php

namespace App\Filament\Resources\PklPeriodes\Pages;

use App\Filament\Resources\PklPeriodes\PklPeriodeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPklPeriode extends EditRecord
{
    protected static string $resource = PklPeriodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
