<?php

namespace App\Filament\Resources\PertemuanKelas;

use App\Filament\Resources\PertemuanKelas\Pages\CreatePertemuanKelas;
use App\Filament\Resources\PertemuanKelas\Pages\EditPertemuanKelas;
use App\Filament\Resources\PertemuanKelas\Pages\ListPertemuanKelas;
use App\Filament\Resources\PertemuanKelas\Schemas\PertemuanKelasForm;
use App\Filament\Resources\PertemuanKelas\Tables\PertemuanKelasTable;
use App\Models\PertemuanKelas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PertemuanKelasResource extends Resource
{
    protected static ?string $model = PertemuanKelas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string | UnitEnum | null $navigationGroup = 'Temp';

    public static function form(Schema $schema): Schema
    {
        return PertemuanKelasForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PertemuanKelasTable::configure($table);
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
            'index' => ListPertemuanKelas::route('/'),
            'create' => CreatePertemuanKelas::route('/create'),
            'edit' => EditPertemuanKelas::route('/{record}/edit'),
        ];
    }
}
