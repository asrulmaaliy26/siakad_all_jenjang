<?php

namespace App\Filament\Resources\TaPengajuanJuduls;

use App\Filament\Resources\TaPengajuanJuduls\Pages\CreateTaPengajuanJudul;
use App\Filament\Resources\TaPengajuanJuduls\Pages\EditTaPengajuanJudul;
use App\Filament\Resources\TaPengajuanJuduls\Pages\ListTaPengajuanJuduls;
use App\Filament\Resources\TaPengajuanJuduls\Pages\ViewTaPengajuanJudul;
use App\Filament\Resources\TaPengajuanJuduls\Schemas\TaPengajuanJudulForm;
use App\Filament\Resources\TaPengajuanJuduls\Schemas\TaPengajuanJudulInfolist;
use App\Filament\Resources\TaPengajuanJuduls\Tables\TaPengajuanJudulsTable;
use App\Models\TaPengajuanJudul;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TaPengajuanJudulResource extends Resource
{
    protected static ?string $model = TaPengajuanJudul::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Pengajuan Judul TA';

    protected static string|UnitEnum|null $navigationGroup = 'Tugas Akhir';

    protected static ?int $navigationSort = 41;

    protected static ?string $recordTitleAttribute = 'judul';

    public static function form(Schema $schema): Schema
    {
        return TaPengajuanJudulForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaPengajuanJudulInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaPengajuanJudulsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user  = \Filament\Facades\Filament::auth()->user();

        // Dosen (pengajar) hanya melihat pengajuan yang ia menjadi pembimbing
        if ($user && $user->isPengajar()) {
            $dosenId = \App\Models\DosenData::where('user_id', $user->id)->value('id');

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

    public static function getPages(): array
    {
        return [
            'index'  => ListTaPengajuanJuduls::route('/'),
            'create' => CreateTaPengajuanJudul::route('/create'),
            'view'   => ViewTaPengajuanJudul::route('/{record}'),
            'edit'   => EditTaPengajuanJudul::route('/{record}/edit'),
        ];
    }
}
