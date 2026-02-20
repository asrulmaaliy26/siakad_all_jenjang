<?php

namespace App\Filament\Resources\SiswaData;

use App\Filament\Resources\SiswaData\Pages\CreateSiswaData;
use App\Filament\Resources\SiswaData\Pages\EditSiswaData;
use App\Filament\Resources\SiswaData\Pages\ListSiswaData;
use App\Filament\Resources\SiswaData\Pages\ViewSiswaData;
use App\Filament\Resources\SiswaData\Schemas\SiswaDataForm;
use App\Filament\Resources\SiswaData\Tables\SiswaDataTable;
use App\Models\SiswaData;
use App\Filament\Resources\SiswaData\RelationManagers\RiwayatPendidikanRelationManager;
use App\Filament\Resources\SiswaData\RelationManagers\AkademikKRSRelationManager;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SiswaDataResource extends Resource
{
    protected static ?string $model = SiswaData::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    // protected static string | UnitEnum | null $navigationGroup = 'Master Data Siswa';
    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    // protected static ?string $navigationLabel = 'Siswa/Mahasiswa ✓';

    public static function getNavigationLabel(): string
    {
        return \App\Helpers\SiakadTerm::pesertaDidik() . ' ✓';
    }

    public static function getModelLabel(): string
    {
        return \App\Helpers\SiakadTerm::pesertaDidik();
    }

    protected static ?int $navigationSort = 16;

    public static function form(Schema $schema): Schema
    {
        return SiswaDataForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiswaDataTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
            RiwayatPendidikanRelationManager::class,
            AkademikKRSRelationManager::class,
            RelationManagers\SiswaDataPendaftarRelationManager::class,
            RelationManagers\SiswaDataOrangTuaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiswaData::route('/'),
            'create' => CreateSiswaData::route('/create'),
            'download-files' => \App\Filament\Resources\SiswaData\SiswaDataResource\Pages\DownloadPublicFiles::route('/download-files'),
            'view' => ViewSiswaData::route('/{record}'),
            'edit' => EditSiswaData::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()
            ->where(function ($query) {
                // Tampilkan siswa yang:
                // 1. Tidak memiliki data pendaftar (siswa lama/input manual)
                // 2. ATAU memiliki data pendaftar dengan Status_Pendaftaran = 'Y' 
                $query->doesntHave('pendaftar')
                    ->orWhereHas('pendaftar', function ($q) {
                        $q->where('Status_Pendaftaran', 'Y');
                    });
            });

        $user = auth()->user();
        if ($user && $user->hasRole('murid') && !$user->hasAnyRole(['super_admin', 'admin'])) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }
}
