<?php

namespace App\Filament\Resources\RiwayatPendidikans\Schemas;

use App\Models\JenjangPendidikan;
use App\Models\Jurusan;
use App\Models\RefOption\StatusSiswa;
use App\Models\SiswaData;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RiwayatPendidikanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_siswa_data')
                    ->label('Data Siswa')
                    ->options(SiswaData::pluck('nama', 'id'))
                    ->searchable(),
                Select::make('id_jenjang_pendidikan')
                    ->label('Jenjang Pendidikan')
                    ->options(JenjangPendidikan::pluck('nama', 'id'))
                    ->searchable(),
                Select::make('id_jurusan')
                    ->label('Jurusan')
                    ->options(Jurusan::pluck('nama', 'id'))
                    ->searchable(),
                Select::make('ro_status_siswa')
                    ->label('Status Siswa')
                    ->options(StatusSiswa::pluck('nilai', 'id'))
                    ->searchable(),
                TextInput::make('angkatan'),
                TextInput::make('nomor_induk'),
                DatePicker::make('tanggal_mulai'),
                DatePicker::make('tanggal_selesai'),
                Select::make('id_wali_dosen')
                    ->label('Wali Dosen')
                    ->relationship('waliDosen', 'nama')
                    ->searchable()
                    ->preload(),
                Select::make('status_aktif')
                    ->options(['Y' => 'Aktif', 'N' => 'Tidak Aktif']),
            ]);
    }
}
