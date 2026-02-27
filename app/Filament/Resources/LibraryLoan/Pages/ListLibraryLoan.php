<?php

namespace App\Filament\Resources\LibraryLoan\Pages;

use App\Filament\Resources\LibraryLoan\LibraryLoanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibraryLoan extends ListRecords
{
    protected static string $resource = LibraryLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
