<?php

namespace App\Filament\Resources\RiwayatPendidikans\Schemas;

use App\Models\Jurusan;
use App\Models\RefOption\StatusSiswa;
use App\Models\RefOption\ProgramKelas;
use App\Models\SiswaData;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_siswa_data')
                    ->label('Data Siswa')
                    ->relationship('siswaData', 'nama')
                    ->searchable()
                    ->preload(false),
                Select::make('id_jurusan')
                    ->label('Jurusan')
                    ->relationship('jurusan', 'nama')
                    ->reactive()
                    ->searchable()
                    ->preload(),
                Select::make('ro_status_siswa')
                    ->label('Status Siswa')
                    ->relationship('statusSiswa', 'nilai')
                    ->searchable()
                    ->preload(),
                Select::make('ro_program_kelas')
                    ->label('Program Kelas')
                    ->relationship('programKelas', 'nilai', fn ($query) => $query->aktif())
                    ->placeholder('Pilih Program Kelas...')
                    ->searchable()
                    ->preload(),
                Select::make('id_tahun_akademik')
                    ->label('Tahun Akademik')
                    ->relationship('tahunAkademik', 'nama')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama} - {$record->periode}")
                    ->default(fn() => \App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id)
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('nomor_induk'),
                DatePicker::make('tanggal_mulai'),
                DatePicker::make('tanggal_selesai'),
                Select::make('id_wali_dosen')
                    ->label('Wali Dosen')
                    ->relationship('waliDosen', 'nama', function ($query, callable $get, ?Model $record) {
                        $jurusanId = $get('id_jurusan');
                        if ($jurusanId) {
                            $query->where('id_jurusan', $jurusanId);

                            if ($record && $record->id_wali_dosen) {
                                $query->orWhere('id', $record->id_wali_dosen);
                            }
                        }
                        return $query;
                    })
                    ->searchable()
                    ->preload(),
                Select::make('status_aktif')
                    ->options(['Y' => 'Aktif', 'N' => 'Tidak Aktif']),
            ]);
    }
}
