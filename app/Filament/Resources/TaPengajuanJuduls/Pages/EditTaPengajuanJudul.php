<?php

namespace App\Filament\Resources\TaPengajuanJuduls\Pages;

use App\Filament\Resources\TaPengajuanJuduls\TaPengajuanJudulResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTaPengajuanJudul extends EditRecord
{
    protected static string $resource = TaPengajuanJudulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
