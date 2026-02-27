<?php

namespace App\Filament\Resources\WisudaMahasiswas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WisudaMahasiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('Tabs')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('Data Akademik & Pribadi')
                            ->schema([
                                Select::make('id_riwayat_pendidikan')
                                    ->label('Mahasiswa')
                                    ->relationship('riwayatPendidikan.siswaData', 'nama')
                                    ->searchable()
                                    ->disabled() // Typically admin shouldn't change who the form belongs to after creation
                                    ->required(),
                                TextInput::make('nama_arab')->label('Nama Lengkap (Huruf Arab)'),
                                TextInput::make('tempat_lahir_arab')->label('Tempat Lahir (Huruf Arab)'),
                                Textarea::make('alamat_malang')->label('Alamat di Malang')->columnSpanFull(),
                                TextInput::make('no_hp')->label('No. Handphone Aktif'),
                                TextInput::make('email')->label('Email Aktif')->email(),
                                \Filament\Forms\Components\FileUpload::make('pas_foto')->label('Pas Foto')->image(),
                                Select::make('id_pembimbing_1')
                                    ->label('Dosen Pembimbing 1')
                                    ->relationship('pembimbing1', 'nama')
                                    ->searchable(),
                                Select::make('id_pembimbing_2')
                                    ->label('Dosen Pembimbing 2')
                                    ->relationship('pembimbing2', 'nama')
                                    ->searchable(),
                            ])->columns(2),

                        \Filament\Schemas\Components\Tabs\Tab::make('Bebas Tanggungan')
                            ->schema([
                                Toggle::make('bebas_prodi')
                                    ->label('Bebas Tanggungan Jurusan/Prodi')
                                    ->helperText('Centang jika mahasiswa telah memenuhi syarat (judul TA, komprehensif, nilai terinput, TOEFL/TOAFL, SKS lulus).'),
                                Toggle::make('bebas_fakultas')
                                    ->label('Bebas Tanggungan Fakultas')
                                    ->helperText('Centang jika terdaftar SK Yudisium, menyerahkan hardcopy/softcopy, dll.'),
                                Toggle::make('bebas_perpustakaan')
                                    ->label('Bebas Tanggungan Perpustakaan')
                                    ->helperText('Mengembalikan buku, unggah E-Theses.'),
                                Toggle::make('bebas_keuangan')
                                    ->label('Bebas Tanggungan Keuangan')
                                    ->helperText('Pembayaran lunas sesuai ketentuan.'),
                            ])->columns(1),

                        \Filament\Schemas\Components\Tabs\Tab::make('Persetujuan')
                            ->schema([
                                Select::make('id_periode_wisuda')
                                    ->label('Periode Wisuda')
                                    ->relationship('periodeWisuda', 'periode_ke')
                                    ->searchable()
                                    ->required(),
                                Select::make('status_pendaftaran')
                                    ->label('Status Pendaftaran')
                                    ->options(['Proses' => 'Proses', 'Disetujui' => 'Disetujui', 'Ditolak' => 'Ditolak'])
                                    ->default('Proses')
                                    ->required(),
                                DateTimePicker::make('tanggal_daftar')
                                    ->label('Tanggal Daftar')
                                    ->required()
                                    ->disabled(),
                            ])->columns(2),
                    ])->columnSpanFull()
            ]);
    }
}
