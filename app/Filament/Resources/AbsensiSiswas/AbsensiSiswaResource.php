<?php

namespace App\Filament\Resources\AbsensiSiswas;

use App\Filament\Resources\AbsensiSiswas\Pages\CreateAbsensiSiswa;
use App\Filament\Resources\AbsensiSiswas\Pages\EditAbsensiSiswa;
use App\Filament\Resources\AbsensiSiswas\Pages\ListAbsensiSiswas;
use App\Filament\Resources\AbsensiSiswas\Schemas\AbsensiSiswaForm;
use App\Filament\Resources\AbsensiSiswas\Tables\AbsensiSiswasTable;
use App\Models\AbsensiSiswa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class AbsensiSiswaResource extends Resource
{
    protected static ?string $model = AbsensiSiswa::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';
    protected static string | UnitEnum | null $navigationGroup = 'Temp';

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return AbsensiSiswaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbsensiSiswasTable::configure($table);
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
            'index' => ListAbsensiSiswas::route('/'),
            'create' => CreateAbsensiSiswa::route('/create'),
            'edit' => EditAbsensiSiswa::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->with([
    //             'pertemuan.mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster',
    //             'krs.riwayatPendidikan.siswa',
    //             'krs.kelas.programKelas',
    //         ]);
    // }
}
