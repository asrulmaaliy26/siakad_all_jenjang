<?php

namespace App\Filament\Resources\SarprasKategoris;

use App\Filament\Resources\SarprasKategoris\Pages\CreateSarprasKategori;
use App\Filament\Resources\SarprasKategoris\Pages\EditSarprasKategori;
use App\Filament\Resources\SarprasKategoris\Pages\ListSarprasKategoris;
use App\Filament\Resources\SarprasKategoris\Schemas\SarprasKategoriForm;
use App\Filament\Resources\SarprasKategoris\Tables\SarprasKategorisTable;
use App\Models\SarprasKategori;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SarprasKategoriResource extends Resource
{
    protected static ?string $model = SarprasKategori::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_kategori';

    protected static string|\UnitEnum|null $navigationGroup = 'Sarpras / Inventaris';
    protected static ?string $navigationLabel = 'Kategori Barang';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return SarprasKategoriForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SarprasKategorisTable::configure($table);
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
            'index' => ListSarprasKategoris::route('/'),
            'create' => CreateSarprasKategori::route('/create'),
            'edit' => EditSarprasKategori::route('/{record}/edit'),
        ];
    }
}
