<?php

namespace App\Filament\Resources\SarprasBarangs;

use App\Filament\Resources\SarprasBarangs\Pages\CreateSarprasBarang;
use App\Filament\Resources\SarprasBarangs\Pages\EditSarprasBarang;
use App\Filament\Resources\SarprasBarangs\Pages\ListSarprasBarangs;
use App\Filament\Resources\SarprasBarangs\Schemas\SarprasBarangForm;
use App\Filament\Resources\SarprasBarangs\Tables\SarprasBarangsTable;
use App\Models\SarprasBarang;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SarprasBarangResource extends Resource
{
    protected static ?string $model = SarprasBarang::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_barang';

    protected static string|\UnitEnum|null $navigationGroup = 'Sarpras / Inventaris';
    protected static ?string $navigationLabel = 'Data Barang';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SarprasBarangForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SarprasBarangsTable::configure($table);
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
            'index' => ListSarprasBarangs::route('/'),
            'create' => CreateSarprasBarang::route('/create'),
            'edit' => EditSarprasBarang::route('/{record}/edit'),
        ];
    }
}
