<?php

namespace App\Filament\Resources\MataPelajaranKurikulums;

use App\Filament\Resources\MataPelajaranKurikulums\Pages\CreateMataPelajaranKurikulum;
use App\Filament\Resources\MataPelajaranKurikulums\Pages\EditMataPelajaranKurikulum;
use App\Filament\Resources\MataPelajaranKurikulums\Pages\ListMataPelajaranKurikulums;
use App\Filament\Resources\MataPelajaranKurikulums\Schemas\MataPelajaranKurikulumForm;
use App\Filament\Resources\MataPelajaranKurikulums\Tables\MataPelajaranKurikulumsTable;
use App\Models\MataPelajaranKurikulum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MataPelajaranKurikulumResource extends Resource
{
    protected static ?string $model = MataPelajaranKurikulum::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string | UnitEnum | null $navigationGroup = 'Temp';

    public static function form(Schema $schema): Schema
    {
        return MataPelajaranKurikulumForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MataPelajaranKurikulumsTable::configure($table);
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
            'index' => ListMataPelajaranKurikulums::route('/'),
            'create' => CreateMataPelajaranKurikulum::route('/create'),
            'edit' => EditMataPelajaranKurikulum::route('/{record}/edit'),
        ];
    }
}
