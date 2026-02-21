<?php

namespace App\Filament\Resources\TaSeminarProposals;

use App\Filament\Resources\TaSeminarProposals\Pages\CreateTaSeminarProposal;
use App\Filament\Resources\TaSeminarProposals\Pages\EditTaSeminarProposal;
use App\Filament\Resources\TaSeminarProposals\Pages\ListTaSeminarProposals;
use App\Filament\Resources\TaSeminarProposals\Pages\ViewTaSeminarProposal;
use App\Filament\Resources\TaSeminarProposals\Schemas\TaSeminarProposalForm;
use App\Filament\Resources\TaSeminarProposals\Schemas\TaSeminarProposalInfolist;
use App\Filament\Resources\TaSeminarProposals\Tables\TaSeminarProposalsTable;
use App\Models\TaSeminarProposal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TaSeminarProposalResource extends Resource
{
    protected static ?string $model = TaSeminarProposal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static ?string $navigationLabel = 'Seminar Proposal';

    protected static string|UnitEnum|null $navigationGroup = 'Tugas Akhir';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'judul';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user  = \Filament\Facades\Filament::auth()->user();

        // Dosen (pengajar) hanya melihat pengajuan yang ia menjadi pembimbing
        if ($user && $user->hasRole('pengajar') && !$user->hasAnyRole(['super_admin', 'admin', 'admin_jenjang'])) {
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
        if ($user && $user->hasRole('murid') && !$user->hasAnyRole(['super_admin', 'admin', 'admin_jenjang'])) {
            $query->whereHas('riwayatPendidikan.siswa', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return TaSeminarProposalForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaSeminarProposalInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaSeminarProposalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTaSeminarProposals::route('/'),
            'create' => CreateTaSeminarProposal::route('/create'),
            'view'   => ViewTaSeminarProposal::route('/{record}'),
            'edit'   => EditTaSeminarProposal::route('/{record}/edit'),
        ];
    }
}
