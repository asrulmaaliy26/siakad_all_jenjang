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

    protected static ?int $navigationSort = 53;

    protected static ?string $recordTitleAttribute = 'judul';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user  = \Filament\Facades\Filament::auth()->user();

        // Dosen (pengajar) hanya melihat pengajuan yang ia menjadi pembimbing
        if ($user && $user->isPengajar()) {
            $dosenId = $user->getDosenId();

            if ($dosenId) {
                $query->where(function ($q) use ($dosenId) {
                    $q->where('id_dosen_pembimbing_1', $dosenId)
                        ->orWhere('id_dosen_pembimbing_2', $dosenId)
                        ->orWhere('id_dosen_pembimbing_3', $dosenId);
                });
            } else {
                // Dosen tidak ditemukan di tabel dosen_data → tidak tampilkan apa-apa
                $query->whereRaw('1 = 0');
            }
        }

        // Murid hanya melihat pengajuan miliknya sendiri (via riwayat_pendidikan)
        if ($user && $user->isMurid()) {
            $query->whereHas('riwayatPendidikan.siswa', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query;
    }

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
