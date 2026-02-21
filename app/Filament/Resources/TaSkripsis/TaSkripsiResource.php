<?php

namespace App\Filament\Resources\TaSkripsis;

use App\Filament\Resources\TaSkripsis\Pages\CreateTaSkripsi;
use App\Filament\Resources\TaSkripsis\Pages\EditTaSkripsi;
use App\Filament\Resources\TaSkripsis\Pages\ListTaSkripsis;
use App\Filament\Resources\TaSkripsis\Pages\ViewTaSkripsi;
use App\Filament\Resources\TaSkripsis\Schemas\TaSkripsiForm;
use App\Filament\Resources\TaSkripsis\Schemas\TaSkripsiInfolist;
use App\Filament\Resources\TaSkripsis\Tables\TaSkripsisTable;
use App\Models\TaSkripsi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TaSkripsiResource extends Resource
{
    protected static ?string $model = TaSkripsi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Sidang Skripsi';

    protected static string|UnitEnum|null $navigationGroup = 'Tugas Akhir';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'judul';

    public static function form(Schema $schema): Schema
    {
        return TaSkripsiForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaSkripsiInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaSkripsisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTaSkripsis::route('/'),
            'create' => CreateTaSkripsi::route('/create'),
            'view'   => ViewTaSkripsi::route('/{record}'),
            'edit'   => EditTaSkripsi::route('/{record}/edit'),
        ];
    }
}
