<?php

namespace App\Filament\Resources\MataPelajaranKelas;

use App\Filament\Resources\MataPelajaranKelas\Pages\CreateMataPelajaranKelas;
use App\Filament\Resources\MataPelajaranKelas\Pages\EditMataPelajaranKelas;
use App\Filament\Resources\MataPelajaranKelas\Pages\ListMataPelajaranKelas;
use App\Filament\Resources\MataPelajaranKelas\Pages\ViewMataPelajaranKelas;
use App\Filament\Resources\MataPelajaranKelas\Schemas\MataPelajaranKelasForm;
use App\Filament\Resources\MataPelajaranKelas\Schemas\MataPelajaranKelasInfolist;
use App\Filament\Resources\MataPelajaranKelas\Tables\MataPelajaranKelasTable;
use App\Models\MataPelajaranKelas;
use BackedEnum;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use UnitEnum;

class MataPelajaranKelasResource extends Resource
{
    protected static ?string $model = MataPelajaranKelas::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $recordTitleAttribute = 'nama';
    protected static string | UnitEnum | null $navigationGroup = 'Perkuliahan';
    protected static ?int $navigationSort = 42;
    // protected static ?string $navigationLabel = 'Perkuliahan';

    public static function getNavigationLabel(): string
    {
        return \App\Helpers\SiakadTerm::mataPelajaranKelas();
    }

    public static function getModelLabel(): string
    {
        return \App\Helpers\SiakadTerm::mataPelajaranKelas();
    }

    public static function form(Schema $schema): Schema
    {
        return MataPelajaranKelasForm::configure($schema);
    }

    // public static function infolist(Infolist $infolist): Infolist
    // {
    //     return MataPelajaranKelasInfolist::configure($infolist);
    // }

    public static function table(Table $table): Table
    {
        return MataPelajaranKelasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AbsensiSiswaRelationManager::class,
            RelationManagers\UjianRelationManager::class,
            RelationManagers\SiswaDataLjkRelationManager::class,
            RelationManagers\JurnalPengajaranRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMataPelajaranKelas::route('/'),
            'create' => CreateMataPelajaranKelas::route('/create'),
            'view' => ViewMataPelajaranKelas::route('/{record}'),
            'edit' => EditMataPelajaranKelas::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = \Filament\Facades\Filament::auth()->user();

        // Jika user memiliki role 'pengajar' dan bukan super_admin/admin
        if ($user && $user->isPengajar()) {
            $query->whereHas('dosenData', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // Jika user memiliki role 'murid' dan bukan super_admin/admin
        if ($user && $user->isMurid()) {
            $query->whereHas('siswaDataLjk.akademikKrs.riwayatPendidikan.siswa', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query;
    }
}
