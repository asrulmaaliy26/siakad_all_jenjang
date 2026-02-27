<?php

namespace App\Filament\Resources\LibraryLoan;

use App\Filament\Resources\LibraryLoan\Pages;
use App\Filament\Resources\LibraryLoan\Schemas\LibraryLoanForm;
use App\Filament\Resources\LibraryLoan\Tables\LibraryLoanTable;
use App\Models\LibraryLoan;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class LibraryLoanResource extends Resource
{
    protected static ?string $model = LibraryLoan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static ?string $navigationLabel = 'Transaksi Peminjaman';
    protected static ?string $modelLabel = 'Peminjaman';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return LibraryLoanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LibraryLoanTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLibraryLoan::route('/'),
            'create' => Pages\CreateLibraryLoan::route('/create'),
            'edit' => Pages\EditLibraryLoan::route('/{record}/edit'),
        ];
    }
}
