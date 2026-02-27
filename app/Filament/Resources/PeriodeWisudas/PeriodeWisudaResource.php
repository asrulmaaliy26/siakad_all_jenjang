<?php

namespace App\Filament\Resources\PeriodeWisudas;

use App\Filament\Resources\PeriodeWisudas\Pages\CreatePeriodeWisuda;
use App\Filament\Resources\PeriodeWisudas\Pages\EditPeriodeWisuda;
use App\Filament\Resources\PeriodeWisudas\Pages\ListPeriodeWisudas;
use App\Filament\Resources\PeriodeWisudas\Pages\ViewPeriodeWisuda;
use App\Filament\Resources\PeriodeWisudas\Schemas\PeriodeWisudaForm;
use App\Filament\Resources\PeriodeWisudas\Schemas\PeriodeWisudaInfolist;
use App\Filament\Resources\PeriodeWisudas\Tables\PeriodeWisudasTable;
use App\Models\PeriodeWisuda;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PeriodeWisudaResource extends Resource
{
    protected static ?string $model = PeriodeWisuda::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Periode Wisuda';
    protected static ?string $pluralModelLabel = 'Periode Wisuda';
    protected static ?string $modelLabel = 'Periode Wisuda';
    protected static string|\UnitEnum|null $navigationGroup = 'Tugas Akhir';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return PeriodeWisudaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PeriodeWisudaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PeriodeWisudasTable::configure($table);
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
            'index' => ListPeriodeWisudas::route('/'),
            'create' => CreatePeriodeWisuda::route('/create'),
            'view' => ViewPeriodeWisuda::route('/{record}'),
            'edit' => EditPeriodeWisuda::route('/{record}/edit'),
        ];
    }
}
