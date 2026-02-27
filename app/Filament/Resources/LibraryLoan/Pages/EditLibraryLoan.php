<?php

namespace App\Filament\Resources\LibraryLoan\Pages;

use App\Filament\Resources\LibraryLoan\LibraryLoanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLibraryLoan extends EditRecord
{
    protected static string $resource = LibraryLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
