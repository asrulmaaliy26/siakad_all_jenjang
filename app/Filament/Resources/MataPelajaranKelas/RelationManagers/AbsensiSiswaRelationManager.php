<?php

namespace App\Filament\Resources\MataPelajaranKelas\RelationManagers;

use App\Filament\Resources\MataPelajaranKelas\MataPelajaranKelasResource;
use App\Models\AkademikKRS;
use App\Models\AbsensiSiswa;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class AbsensiSiswaRelationManager extends RelationManager
{
    protected static string $relationship = 'absensiSiswa';

    protected static ?string $title = 'Absensi';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) => $query
                    ->with(['krs.riwayatPendidikan.siswaData'])
                    ->orderBy('waktu_absen', 'desc')
            )
            ->columns([
                TextColumn::make('krs.riwayatPendidikan.siswaData.nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('krs.riwayatPendidikan.siswaData.nomor_induk') // Changed from nis to nomor_induk based on typical schema, user query said sd.nomor_induk
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Kuliah')
                    ->toggleable(isToggledHiddenByDefault: true), // Hidden by default as it's the parent resource
                TextColumn::make('mataPelajaranKelas.dosenData.nama')
                    ->label('Dosen')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('waktu_absen')
                    ->label('Waktu Absen')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Absensi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Izin' => 'warning',
                        'Sakit' => 'danger',
                        'Alpa' => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),
                // Columns for Hari, Jam, Ruang from Parent
                TextColumn::make('mataPelajaranKelas.hari')
                    ->label('Hari')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('mataPelajaranKelas.jam')
                    ->label('Jam')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('mataPelajaranKelas.ruang.nilai') // Assuming relation to reference_option
                    ->label('Ruang')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter berdasarkan grup tanggal (Sesi Absensi)
                SelectFilter::make('sesi_absensi')
                    ->label('Pilih Sesi Absensi')
                    ->options(function () {
                        // Ambil semua tanggal unik dari database
                        $dates = AbsensiSiswa::whereHas('mataPelajaranKelas', function ($query) {
                            $query->where('id', $this->getOwnerRecord()->id);
                        })
                            ->selectRaw('DATE(waktu_absen) as tanggal, TIME(waktu_absen) as jam')
                            ->orderBy('waktu_absen', 'desc')
                            ->get()
                            ->groupBy('tanggal')
                            ->mapWithKeys(function ($items, $tanggal) {
                                $carbonDate = Carbon::parse($tanggal);
                                $formattedDate = $carbonDate->format('d M Y');

                                // Kelompokkan berdasarkan jam dalam satu tanggal
                                $jamList = $items->pluck('jam')->map(function ($jam) {
                                    return Carbon::parse($jam)->format('H:i');
                                })->unique()->implode(', ');

                                // Hitung jumlah siswa yang hadir pada sesi tersebut
                                $totalSiswa = AbsensiSiswa::whereHas('mataPelajaranKelas', function ($query) {
                                    $query->where('id', $this->getOwnerRecord()->id);
                                })
                                    ->whereDate('waktu_absen', $tanggal)
                                    ->count();

                                return [$tanggal => "{$formattedDate} - {$jamList} ({$totalSiswa} siswa)"];
                            });

                        return $dates;
                    })
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return;
                        }

                        // Filter berdasarkan tanggal yang dipilih
                        $query->whereDate('waktu_absen', $data['value']);
                    }),

                // Filter range tanggal
                Filter::make('range_tanggal')
                    ->form([
                        DateTimePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->native(false)
                            ->displayFormat('d M Y'),
                        DateTimePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai')
                            ->native(false)
                            ->displayFormat('d M Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tanggal_mulai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('waktu_absen', '>=', Carbon::parse($date)->format('Y-m-d')),
                            )
                            ->when(
                                $data['tanggal_selesai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('waktu_absen', '<=', Carbon::parse($date)->format('Y-m-d')),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        $indicators = [];

                        if ($data['tanggal_mulai'] ?? null) {
                            $indicators[] = 'Dari: ' . Carbon::parse($data['tanggal_mulai'])->format('d M Y');
                        }

                        if ($data['tanggal_selesai'] ?? null) {
                            $indicators[] = 'Sampai: ' . Carbon::parse($data['tanggal_selesai'])->format('d M Y');
                        }

                        return implode(', ', $indicators);
                    }),

                // Filter berdasarkan status
                SelectFilter::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                        'Alpa' => 'Alpa',
                    ]),

                // Filter berdasarkan bulan/tahun
                SelectFilter::make('periode')
                    ->label('Periode')
                    ->options(function () {
                        $periods = AbsensiSiswa::whereHas('mataPelajaranKelas', function ($query) {
                            $query->where('id', $this->getOwnerRecord()->id);
                        })
                            ->selectRaw('YEAR(waktu_absen) as tahun, MONTH(waktu_absen) as bulan')
                            ->distinct()
                            ->orderBy('tahun', 'desc')
                            ->orderBy('bulan', 'desc')
                            ->get()
                            ->mapWithKeys(function ($item) {
                                $bulanIndonesia = [
                                    1 => 'Januari',
                                    2 => 'Februari',
                                    3 => 'Maret',
                                    4 => 'April',
                                    5 => 'Mei',
                                    6 => 'Juni',
                                    7 => 'Juli',
                                    8 => 'Agustus',
                                    9 => 'September',
                                    10 => 'Oktober',
                                    11 => 'November',
                                    12 => 'Desember'
                                ];
                                $key = $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
                                $value = $bulanIndonesia[$item->bulan] . ' ' . $item->tahun;
                                return [$key => $value];
                            });

                        return $periods;
                    })
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return;
                        }

                        [$tahun, $bulan] = explode('-', $data['value']);
                        $query->whereYear('waktu_absen', $tahun)
                            ->whereMonth('waktu_absen', $bulan);
                    }),
            ])
            ->headerActions([
                Action::make('rekap_absensi')
                    ->label('Rekap Absensi')
                    ->icon('heroicon-o-document-chart-bar')
                    ->modalContent(function ($livewire) {
                        $record = $livewire->getOwnerRecord();
                        $data = \Illuminate\Support\Facades\DB::select("
                            SELECT 
                                sd.id AS id_siswa,
                                sd.nama AS nama_siswa,
                                sd.nomor_induk AS nim,
                                mp.nama AS mata_kuliah,
                                mp.bobot AS sks,
                                dd.nama AS dosen_pengampu,
                                mpk.hari,
                                mpk.jam,
                                mpk.tanggal AS jadwal_tanggal,
                                COUNT(CASE WHEN a.status = 'Hadir' THEN 1 END) AS total_hadir,
                                COUNT(CASE WHEN a.status = 'Izin' THEN 1 END) AS total_izin,
                                COUNT(CASE WHEN a.status = 'Sakit' THEN 1 END) AS total_sakit,
                                COUNT(CASE WHEN a.status = 'Alpa' THEN 1 END) AS total_alpha,
                                COUNT(a.id) AS total_absensi,
                                ROUND((COUNT(CASE WHEN a.status = 'Hadir' THEN 1 END) * 100.0 / NULLIF(COUNT(a.id), 0)), 2) AS persentase_hadir,
                                CASE 
                                    WHEN (COUNT(CASE WHEN a.status = 'Hadir' THEN 1 END) * 100.0 / NULLIF(COUNT(a.id), 0)) >= 80 THEN 'LULUS ABSEN'
                                    WHEN COUNT(a.id) = 0 THEN 'BELUM ABSEN'
                                    ELSE 'TIDAK LULUS'
                                END AS status_kelulusan_absensi
                            FROM akademik_krs ak
                            JOIN riwayat_pendidikan rp ON ak.id_riwayat_pendidikan = rp.id
                            JOIN siswa_data sd ON rp.id_siswa_data = sd.id
                            CROSS JOIN mata_pelajaran_kelas mpk
                            LEFT JOIN absensi_siswa a ON a.id_krs = ak.id AND a.id_mata_pelajaran_kelas = mpk.id
                            JOIN mata_pelajaran_kurikulum mpkur ON mpk.id_mata_pelajaran_kurikulum = mpkur.id
                            JOIN mata_pelajaran_master mp ON mpkur.id_mata_pelajaran_master = mp.id
                            LEFT JOIN dosen_data dd ON mpk.id_dosen_data = dd.id
                            WHERE mpk.id = ? AND ak.id_kelas = mpk.id_kelas
                            GROUP BY sd.id, sd.nama, sd.nomor_induk, mp.nama, mp.bobot, dd.nama, mpk.hari, mpk.jam, mpk.tanggal
                        ", [$record->id]);

                        return view('filament.resources.mata-pelajaran-kelas.rekap-absensi', [
                            'records' => $data,
                        ]);
                    })
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
                Action::make('create_session')
                    ->label('Buat Sesi Absensi')
                    ->form(function ($livewire) {
                        $record = $livewire->getOwnerRecord();
                        $krsList = AkademikKRS::where('id_kelas', $record->id_kelas)
                            ->with('riwayatPendidikan.siswaData')
                            ->get();

                        return [
                            DateTimePicker::make('waktu_absen')
                                ->required()
                                ->default(now())
                                ->label('Tanggal & Waktu Absensi')
                                ->native(false)
                                ->displayFormat('d M Y H:i'),
                            Repeater::make('students')
                                ->label('Daftar Mahasiswa')
                                ->schema([
                                    Hidden::make('id_krs'),
                                    TextInput::make('nama')
                                        ->hiddenLabel()
                                        ->disabled()
                                        ->columnSpan(3),
                                    Select::make('status')
                                        ->hiddenLabel()
                                        ->options([
                                            'Hadir' => 'Hadir',
                                            'Izin' => 'Izin',
                                            'Sakit' => 'Sakit',
                                            'Alpa' => 'Alpa',
                                        ])
                                        ->default('Hadir')
                                        ->required()
                                        ->selectablePlaceholder(false)
                                        ->native(false)
                                        ->columnSpan(1),
                                ])
                                ->columns(4)
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false)
                                ->columnSpan(2)
                                ->default(
                                    $krsList->map(function ($krs) {
                                        return [
                                            'id_krs' => $krs->id,
                                            'nama' => $krs->riwayatPendidikan->siswaData->nama ?? '-',
                                            'status' => 'Hadir',
                                        ];
                                    })->toArray()
                                ),
                        ];
                    })
                    ->action(function (array $data, $livewire) {
                        $record = $livewire->getOwnerRecord();

                        foreach ($data['students'] as $studentData) {
                            AbsensiSiswa::create([
                                'id_mata_pelajaran_kelas' => $record->id,
                                'id_krs' => $studentData['id_krs'],
                                'waktu_absen' => $data['waktu_absen'],
                                'status' => $studentData['status'],
                            ]);
                        }

                        Notification::make()
                            ->title('Sesi absensi berhasil dibuat')
                            ->success()
                            ->send();
                    })
                    ->disabled(fn() => auth()->user()->hasRole('murid') && !auth()->user()->hasAnyRole(['super_admin', 'admin'])),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        DateTimePicker::make('waktu_absen')
                            ->required()
                            ->label('Tanggal & Waktu')
                            ->native(false)
                            ->displayFormat('d M Y H:i'),
                        Select::make('status')
                            ->options([
                                'Hadir' => 'Hadir',
                                'Izin' => 'Izin',
                                'Sakit' => 'Sakit',
                                'Alpa' => 'Alpa',
                            ])
                            ->required()
                            ->native(false),
                    ]),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('waktu_absen', 'desc')
            ->striped()
            ->poll('60s');
    }
}
