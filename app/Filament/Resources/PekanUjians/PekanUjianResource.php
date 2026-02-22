<?php

namespace App\Filament\Resources\PekanUjians;

use App\Filament\Resources\PekanUjians\Pages\CreatePekanUjian;
use App\Filament\Resources\PekanUjians\Pages\EditPekanUjian;
use App\Filament\Resources\PekanUjians\Pages\ListPekanUjians;
use App\Filament\Resources\PekanUjians\Pages\ViewPekanUjian;
use App\Filament\Resources\PekanUjians\Schemas\PekanUjianForm;
use App\Filament\Resources\PekanUjians\Schemas\PekanUjianInfolist;
use App\Filament\Resources\PekanUjians\Tables\PekanUjiansTable;
use App\Filament\Resources\PekanUjians\RelationManagers\MataPelajaranKelasRelationManager;
use App\Models\PekanUjian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PekanUjianResource extends Resource
{
    protected static ?string $model = PekanUjian::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $recordTitleAttribute = 'nama';
    protected static string | UnitEnum | null $navigationGroup = 'Perkuliahan';
    protected static ?int $navigationSort = 24;
    protected static ?string $navigationLabel = 'Ujian';

    public static function form(Schema $schema): Schema
    {
        return PekanUjianForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PekanUjianInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PekanUjiansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\MataPelajaranKelasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPekanUjians::route('/'),
            'create' => CreatePekanUjian::route('/create'),
            'view' => ViewPekanUjian::route('/{record}'),
            'edit' => EditPekanUjian::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = \Filament\Facades\Filament::auth()->user();

        // Optional: Filter Pekan Ujian itself if needed for students
        // if ($user && $user->hasRole('murid') && !$user->hasAnyRole(['super_admin', 'admin'])) {
        //     $query->whereHas('tahunAkademik.kelas.mataPelajaranKelas.siswaDataLjk.akademikKrs.riwayatPendidikan.siswa', function ($q) use ($user) {
        //         $q->where('user_id', $user->id);
        //     });
        // }

        return $query;
    }
}
