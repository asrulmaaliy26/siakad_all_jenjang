<?php

namespace App\Filament\Resources\MataPelajaranMasters;

use App\Filament\Resources\MataPelajaranMasters\Pages\CreateMataPelajaranMaster;
use App\Filament\Resources\MataPelajaranMasters\Pages\EditMataPelajaranMaster;
use App\Filament\Resources\MataPelajaranMasters\Pages\ListMataPelajaranMasters;
use App\Filament\Resources\MataPelajaranMasters\Schemas\MataPelajaranMasterForm;
use App\Filament\Resources\MataPelajaranMasters\Tables\MataPelajaranMastersTable;
use App\Models\MataPelajaranMaster;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MataPelajaranMasterResource extends Resource
{
    protected static ?string $model = MataPelajaranMaster::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bookmark-square';
    protected static string | UnitEnum | null $navigationGroup = 'Master Data';
    // protected static ?string $navigationLabel = 'Mata Pelajaran';

    public static function getNavigationLabel(): string
    {
        return \App\Helpers\SiakadTerm::mataPelajaran() . '';
    }

    public static function getModelLabel(): string
    {
        return \App\Helpers\SiakadTerm::mataPelajaran();
    }
    protected static ?int $navigationSort = 12;

    public static function form(Schema $schema): Schema
    {
        return MataPelajaranMasterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MataPelajaranMastersTable::configure($table);
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
            'index' => ListMataPelajaranMasters::route('/'),
            'create' => CreateMataPelajaranMaster::route('/create'),
            'edit' => EditMataPelajaranMaster::route('/{record}/edit'),
        ];
    }
}
