<?php

namespace App\Filament\Resources\LibraryVisit;

use App\Filament\Resources\LibraryVisit\Pages;
use App\Filament\Resources\LibraryVisit\Schemas\LibraryVisitForm;
use App\Filament\Resources\LibraryVisit\Tables\LibraryVisitTable;
use App\Models\LibraryVisit;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class LibraryVisitResource extends Resource
{
    protected static ?string $model = LibraryVisit::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static ?string $navigationLabel = 'Kunjungan Mahasiswa';
    protected static ?string $modelLabel = 'Kunjungan';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return LibraryVisitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LibraryVisitTable::configure($table);
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
            'index' => Pages\ListLibraryVisit::route('/'),
            'create' => Pages\CreateLibraryVisit::route('/create'),
            'edit' => Pages\EditLibraryVisit::route('/{record}/edit'),
        ];
    }
}
