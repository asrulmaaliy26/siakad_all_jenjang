<?php

namespace App\Filament\Resources\SarprasSuratKategoris;

use App\Filament\Resources\SarprasSuratKategoris\Pages\CreateSarprasSuratKategori;
use App\Filament\Resources\SarprasSuratKategoris\Pages\EditSarprasSuratKategori;
use App\Filament\Resources\SarprasSuratKategoris\Pages\ListSarprasSuratKategoris;
use App\Filament\Resources\SarprasSuratKategoris\Schemas\SarprasSuratKategoriForm;
use App\Filament\Resources\SarprasSuratKategoris\Tables\SarprasSuratKategorisTable;
use App\Models\SarprasSuratKategori;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SarprasSuratKategoriResource extends Resource
{
    protected static ?string $model = SarprasSuratKategori::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;
    protected static string|\UnitEnum|null $navigationGroup = 'Sarpras / Inventaris';
    protected static ?string $navigationLabel = 'Kategori Surat';
    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return SarprasSuratKategoriForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SarprasSuratKategorisTable::configure($table);
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
            'index' => ListSarprasSuratKategoris::route('/'),
            'create' => CreateSarprasSuratKategori::route('/create'),
            'edit' => EditSarprasSuratKategori::route('/{record}/edit'),
        ];
    }
}
