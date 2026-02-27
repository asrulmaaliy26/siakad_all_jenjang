<?php

namespace App\Filament\Resources\WisudaMahasiswas;

use App\Filament\Resources\WisudaMahasiswas\Pages\CreateWisudaMahasiswa;
use App\Filament\Resources\WisudaMahasiswas\Pages\EditWisudaMahasiswa;
use App\Filament\Resources\WisudaMahasiswas\Pages\ListWisudaMahasiswas;
use App\Filament\Resources\WisudaMahasiswas\Pages\ViewWisudaMahasiswa;
use App\Filament\Resources\WisudaMahasiswas\Schemas\WisudaMahasiswaForm;
use App\Filament\Resources\WisudaMahasiswas\Schemas\WisudaMahasiswaInfolist;
use App\Filament\Resources\WisudaMahasiswas\Tables\WisudaMahasiswasTable;
use App\Models\WisudaMahasiswa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WisudaMahasiswaResource extends Resource
{
    protected static ?string $model = WisudaMahasiswa::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Wisuda Mahasiswa';
    protected static ?string $pluralModelLabel = 'Data Wisuda Mahasiswa';
    protected static ?string $modelLabel = 'Wisuda Mahasiswa';
    protected static string|\UnitEnum|null $navigationGroup = 'Tugas Akhir';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return WisudaMahasiswaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WisudaMahasiswaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WisudaMahasiswasTable::configure($table);
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
            'index' => ListWisudaMahasiswas::route('/'),
            'create' => CreateWisudaMahasiswa::route('/create'),
            'view' => ViewWisudaMahasiswa::route('/{record}'),
            'edit' => EditWisudaMahasiswa::route('/{record}/edit'),
        ];
    }
}
