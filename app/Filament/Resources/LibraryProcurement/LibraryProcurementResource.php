<?php

namespace App\Filament\Resources\LibraryProcurement;

use App\Filament\Resources\LibraryProcurement\Pages;
use App\Filament\Resources\LibraryProcurement\Schemas\LibraryProcurementForm;
use App\Filament\Resources\LibraryProcurement\Tables\LibraryProcurementTable;
use App\Models\LibraryProcurement;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class LibraryProcurementResource extends Resource
{
    protected static ?string $model = LibraryProcurement::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';
    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static ?string $navigationLabel = 'Transaksi Pembelian';
    protected static ?string $modelLabel = 'Pembelian';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return LibraryProcurementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LibraryProcurementTable::configure($table);
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
            'index' => Pages\ListLibraryProcurement::route('/'),
            'create' => Pages\CreateLibraryProcurement::route('/create'),
            'edit' => Pages\EditLibraryProcurement::route('/{record}/edit'),
        ];
    }
}
