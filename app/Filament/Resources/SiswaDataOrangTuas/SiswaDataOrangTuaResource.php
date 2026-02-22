<?php

namespace App\Filament\Resources\SiswaDataOrangTuas;

use App\Filament\Resources\SiswaDataOrangTuas\Pages\CreateSiswaDataOrangTua;
use App\Filament\Resources\SiswaDataOrangTuas\Pages\EditSiswaDataOrangTua;
use App\Filament\Resources\SiswaDataOrangTuas\Pages\ListSiswaDataOrangTuas;
use App\Filament\Resources\SiswaDataOrangTuas\Schemas\SiswaDataOrangTuaForm;
use App\Filament\Resources\SiswaDataOrangTuas\Tables\SiswaDataOrangTuasTable;
use App\Models\SiswaDataOrangTua;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SiswaDataOrangTuaResource extends Resource
{
    protected static ?string $model = SiswaDataOrangTua::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static string | UnitEnum | null $navigationGroup = 'Temp';

    public static function form(Schema $schema): Schema
    {
        return SiswaDataOrangTuaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiswaDataOrangTuasTable::configure($table);
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
            'index' => ListSiswaDataOrangTuas::route('/'),
            'create' => CreateSiswaDataOrangTua::route('/create'),
            'edit' => EditSiswaDataOrangTua::route('/{record}/edit'),
        ];
    }
}
