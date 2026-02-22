<?php

namespace App\Filament\Resources\RiwayatPendidikans;

use App\Filament\Resources\RiwayatPendidikans\Pages\CreateRiwayatPendidikan;
use App\Filament\Resources\RiwayatPendidikans\Pages\EditRiwayatPendidikan;
use App\Filament\Resources\RiwayatPendidikans\Pages\ListRiwayatPendidikans;
use App\Filament\Resources\RiwayatPendidikans\Pages\ViewRiwayatPendidikan;
use App\Filament\Resources\RiwayatPendidikans\Schemas\RiwayatPendidikanForm;
use App\Filament\Resources\RiwayatPendidikans\Tables\RiwayatPendidikansTable;
use App\Models\RiwayatPendidikan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use App\Filament\Resources\RiwayatPendidikans\RelationManagers\AkademikKRSRelationManager;
use App\Models\AkademikKrs;

class RiwayatPendidikanResource extends Resource
{
    protected static ?string $model = RiwayatPendidikan::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static string | UnitEnum | null $navigationGroup = 'Temp';

    // protected static ?string $navigationLabel = 'Siswa/Mahasiswa';

    // protected static ?int $navigationSort = 16;

    public static function form(Schema $schema): Schema
    {
        return RiwayatPendidikanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RiwayatPendidikansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
            AkademikKRSRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRiwayatPendidikans::route('/'),
            'create' => CreateRiwayatPendidikan::route('/create'),
            'view' => ViewRiwayatPendidikan::route('/{record}'),
            'edit' => EditRiwayatPendidikan::route('/{record}/edit'),
        ];
    }
}
