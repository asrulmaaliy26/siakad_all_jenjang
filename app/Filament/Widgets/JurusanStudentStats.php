<?php

namespace App\Filament\Widgets;

use App\Models\Jurusan;
use App\Models\RiwayatPendidikan;
use App\Models\SiswaDataPendaftar;
use App\Models\TahunAkademik;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class JurusanStudentStats extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Rincian Mahasiswa Per Program Studi';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()->can('View:JurusanStudentStats');
    }

    public function table(Table $table): Table
    {
        $activeTahunId = $this->filters['tahun_akademik'] ?? null;
        $tahunAkademik = $activeTahunId ? TahunAkademik::find($activeTahunId) : null;
        $tahunNamaBare = $tahunAkademik ? $tahunAkademik->getRawOriginal('nama') : null;
        $tahunLabel    = $tahunNamaBare ?? 'Semua Tahun';

        // Kumpulkan semua ID tahun akademik dengan nama tahun yang sama (Ganjil + Genap)
        $tahunIds = $tahunNamaBare
            ? TahunAkademik::where('nama', $tahunNamaBare)->pluck('id')
            : null;

        return $table
            ->query(
                Jurusan::query()->where('nama', 'NOT LIKE', '%temp%')

                    ->withCount([
                        // Mhs. Aktif: ro_status_siswa = 37, filter via KRS id_tahun_akademik (Ganjil+Genap)
                        'riwayatPendidikan as total_aktif' => function (Builder $query) use ($tahunIds) {
                            $query->where('ro_status_siswa', 37);
                            if ($tahunIds) {
                                $query->whereHas(
                                    'akademikKrs',
                                    fn($q) => $q->whereIn('id_tahun_akademik', $tahunIds)
                                );
                            }
                        },
                        // Pending: ro_status_siswa IN [142, 43], filter id_tahun_akademik di riwayat
                        'riwayatPendidikan as total_pending' => function (Builder $query) use ($tahunIds) {
                            $query->whereIn('ro_status_siswa', [142, 43]);
                            if ($tahunIds) {
                                $query->whereIn('id_tahun_akademik', $tahunIds);
                            }
                        },
                        // Calon Mahasiswa: belum diterima (Status_Pendaftaran != 'Y')
                        'pendaftar as total_pendaftar_baru' => function (Builder $query) use ($tahunIds) {
                            $query->where(function ($q) {
                                $q->where('Status_Pendaftaran', '!=', 'Y')
                                    ->orWhereNull('Status_Pendaftaran');
                            });
                            if ($tahunIds) {
                                $query->whereIn('id_tahun_akademik', $tahunIds);
                            }
                        },
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Program Studi')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->color('primary')
                    ->description(fn(Jurusan $record): string => $record->fakultas->nama ?? 'Tanpa Fakultas'),

                Tables\Columns\TextColumn::make('total_aktif')
                    ->label('Mhs. Aktif')
                    ->sortable()
                    ->icon('heroicon-m-user-group')
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => $state . ' Orang'),

                Tables\Columns\TextColumn::make('total_pending')
                    ->label('Pending / Non-Aktif')
                    ->sortable()
                    ->icon('heroicon-m-clock')
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => $state . ' Orang'),

                Tables\Columns\TextColumn::make('total_pendaftar_baru')
                    ->label('Calon Mahasiswa')
                    ->sortable()
                    ->icon('heroicon-m-user-plus')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => $state . ' Orang')
                    ->description('Data Pendaftaran'),
            ])
            ->paginated([10, 25, 50])
            ->defaultSort('total_aktif', 'desc')
            ->emptyStateHeading('Tidak ada data statistik untuk periode ini');
    }
}
