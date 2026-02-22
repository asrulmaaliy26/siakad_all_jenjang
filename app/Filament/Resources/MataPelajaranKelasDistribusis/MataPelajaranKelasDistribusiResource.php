<?php

namespace App\Filament\Resources\MataPelajaranKelasDistribusis;

use App\Filament\Resources\MataPelajaranKelasDistribusis\Pages\CreateMataPelajaranKelasDistribusi;
use App\Filament\Resources\MataPelajaranKelasDistribusis\Pages\EditMataPelajaranKelasDistribusi;
use App\Filament\Resources\MataPelajaranKelasDistribusis\Pages\ListMataPelajaranKelasDistribusis;
use App\Filament\Resources\MataPelajaranKelasDistribusis\Schemas\MataPelajaranKelasDistribusiForm;
use App\Filament\Resources\MataPelajaranKelasDistribusis\Tables\MataPelajaranKelasDistribusisTable;
use App\Models\MataPelajaranKelasDistribusi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MataPelajaranKelasDistribusiResource extends Resource
{
    protected static ?string $model = MataPelajaranKelasDistribusi::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $recordTitleAttribute = 'nama';
    protected static string | UnitEnum | null $navigationGroup = 'Perkuliahan';
    protected static ?int $navigationSort = 41;
    // protected static ?string $navigationLabel = 'Distribusi Mata Kuliah ✓';

    public static function getNavigationLabel(): string
    {
        return \App\Helpers\SiakadTerm::mataPelajaran() . ' Distribusi ✓';
    }

    public static function getModelLabel(): string
    {
        return \App\Helpers\SiakadTerm::mataPelajaran() . ' Distribusi';
    }

    public static function form(Schema $schema): Schema
    {
        return MataPelajaranKelasDistribusiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MataPelajaranKelasDistribusisTable::configure($table);
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
            'index' => ListMataPelajaranKelasDistribusis::route('/'),
            'create' => CreateMataPelajaranKelasDistribusi::route('/create'),
            'edit' => EditMataPelajaranKelasDistribusi::route('/{record}/edit'),
        ];
    }
}
