<?php

namespace App\Filament\Resources\SarprasPeminjamen;

use App\Filament\Resources\SarprasPeminjamen\Pages\CreateSarprasPeminjaman;
use App\Filament\Resources\SarprasPeminjamen\Pages\EditSarprasPeminjaman;
use App\Filament\Resources\SarprasPeminjamen\Pages\ListSarprasPeminjamen;
use App\Filament\Resources\SarprasPeminjamen\Schemas\SarprasPeminjamanForm;
use App\Filament\Resources\SarprasPeminjamen\Tables\SarprasPeminjamenTable;
use App\Models\SarprasPeminjaman;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SarprasPeminjamanResource extends Resource
{
    protected static ?string $model = SarprasPeminjaman::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|\UnitEnum|null $navigationGroup = 'Sarpras / Inventaris';
    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'no';

    public static function form(Schema $schema): Schema
    {
        return SarprasPeminjamanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SarprasPeminjamenTable::configure($table);
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
            'index' => ListSarprasPeminjamen::route('/'),
            'create' => CreateSarprasPeminjaman::route('/create'),
            'edit' => EditSarprasPeminjaman::route('/{record}/edit'),
        ];
    }
}
