<?php

namespace App\Filament\Resources\JenjangPendidikans;

use App\Filament\Resources\JenjangPendidikans\Pages\CreateJenjangPendidikan;
use App\Filament\Resources\JenjangPendidikans\Pages\EditJenjangPendidikan;
use App\Filament\Resources\JenjangPendidikans\Pages\ListJenjangPendidikans;
use App\Filament\Resources\JenjangPendidikans\Schemas\JenjangPendidikanForm;
use App\Filament\Resources\JenjangPendidikans\Tables\JenjangPendidikansTable;
use App\Models\JenjangPendidikan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class JenjangPendidikanResource extends Resource
{
    protected static ?string $model = JenjangPendidikan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';
    protected static string | UnitEnum | null $navigationGroup = 'Temp';

    public static function form(Schema $schema): Schema
    {
        return JenjangPendidikanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JenjangPendidikansTable::configure($table);
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
            'index' => ListJenjangPendidikans::route('/'),
            'create' => CreateJenjangPendidikan::route('/create'),
            'edit' => EditJenjangPendidikan::route('/{record}/edit'),
        ];
    }
}
