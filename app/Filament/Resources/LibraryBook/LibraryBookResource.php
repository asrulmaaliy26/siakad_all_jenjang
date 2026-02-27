<?php

namespace App\Filament\Resources\LibraryBook;

use App\Filament\Resources\LibraryBook\Pages;
use App\Filament\Resources\LibraryBook\Schemas\LibraryBookForm;
use App\Filament\Resources\LibraryBook\Tables\LibraryBookTable;
use App\Models\LibraryBook;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class LibraryBookResource extends Resource
{
    protected static ?string $model = LibraryBook::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static ?string $navigationLabel = 'Bank Data Buku';
    protected static ?string $modelLabel = 'Buku';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return LibraryBookForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LibraryBookTable::configure($table);
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
            'index' => Pages\ListLibraryBook::route('/'),
            'create' => Pages\CreateLibraryBook::route('/create'),
            'edit' => Pages\EditLibraryBook::route('/{record}/edit'),
        ];
    }
}
