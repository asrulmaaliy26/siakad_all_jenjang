<?php

namespace App\Filament\Resources\TahunAkademiks;

use App\Filament\Resources\TahunAkademiks\Pages\CreateTahunAkademik;
use App\Filament\Resources\TahunAkademiks\Pages\EditTahunAkademik;
use App\Filament\Resources\TahunAkademiks\Pages\ListTahunAkademiks;
use App\Filament\Resources\TahunAkademiks\Schemas\TahunAkademikForm;
use App\Filament\Resources\TahunAkademiks\Tables\TahunAkademiksTable;
use App\Models\TahunAkademik;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TahunAkademikResource extends Resource
{
    protected static ?string $model = TahunAkademik::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar';
    protected static string | UnitEnum | null $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Tahun Akademik';
    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return TahunAkademikForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TahunAkademiksTable::configure($table);
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
            'index' => ListTahunAkademiks::route('/'),
            'create' => CreateTahunAkademik::route('/create'),
            'edit' => EditTahunAkademik::route('/{record}/edit'),
        ];
    }
}
