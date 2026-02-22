<?php

namespace App\Filament\Resources\SiswaDataPendaftars\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class SiswaDataPendaftarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Data Pendaftar')
                    ->tabs([
                        // Tab 1: Data Dasar
                        Tabs\Tab::make('Data Dasar')
                            ->schema([
                                Section::make('Informasi Pendaftaran')
                                    ->schema([
                                        Select::make('id_siswa_data')
                                            ->label('Pilih Siswa')
                                            ->relationship('siswa', 'nama')
                                            ->searchable()
                                            ->preload()
                                            ->visible(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),

                                        Group::make()
                                            ->relationship('siswa')
                                            ->schema([
                                                TextInput::make('nama')
                                                    ->label('Nama Siswa (Data Utama)')
                                                    ->required(),
                                                TextInput::make('nama_lengkap')
                                                    ->label('Nama Lengkap (Data Utama)')
                                                    ->maxLength(255),
                                            ])
                                            ->visible(fn($livewire) => !($livewire instanceof \Filament\Resources\Pages\CreateRecord)),

                                        TextInput::make('No_Pendaftaran')
                                            ->label('No. Pendaftaran')
                                            ->maxLength(255)
                                            ->disabled(fn() => auth()->user()->isMurid()),

                                        TextInput::make('Tahun_Masuk')
                                            ->label('Tahun Masuk')
                                            ->maxLength(4)
                                            ->disabled(fn() => auth()->user()->isMurid()),

                                        DatePicker::make('Tgl_Daftar')
                                            ->label('Tanggal Daftar')
                                            ->default(now())
                                            ->disabled(fn() => auth()->user()->isMurid()),
                                    ])
                                    ->columns(2),

                                Section::make('Program Studi')
                                    ->schema([
                                        Select::make('ro_program_sekolah')
                                            ->label('Program Sekolah')
                                            ->relationship('programSekolahRef', 'nilai', function ($query) {
                                                return $query->where('nama_grup', 'program_sekolah')
                                                    ->where('status', 1);
                                            })
                                            ->searchable()
                                            // ->required()
                                            ->preload()
                                            ->disabled(fn() => auth()->user()->isMurid()),
                                        // ->reactive()
                                        // ->afterStateUpdated(function ($state, callable $set) {
                                        //     // Logic removed as id_jenjang_pendidikan is removed
                                        // }),

                                        // Select::make('id_jenjang_pendidikan') // Removed
                                        //     ->label('Jenjang Pendidikan')
                                        //     ->relationship('jenjangPendidikan', 'nama')
                                        //     ->searchable()
                                        //     ->preload()
                                        //     ->required() // Sebaiknya required agar data konsisten
                                        //     ->dehydrated(),

                                        TextInput::make('Kelas_Program_Kuliah')
                                            ->label('Kelas Program'),

                                        Select::make('id_jurusan')
                                            ->label('Jurusan')
                                            ->relationship('jurusan', 'nama')
                                            ->searchable()
                                            ->preload()
                                            ->disabled(fn() => auth()->user()->isMurid()),

                                        Select::make('Prodi_Pilihan_1')
                                            ->label('Prodi Pilihan 1')
                                            ->options(\App\Models\Jurusan::pluck('nama', 'nama'))
                                            ->searchable(),

                                        Select::make('Prodi_Pilihan_2')
                                            ->label('Prodi Pilihan 2')
                                            ->options(\App\Models\Jurusan::pluck('nama', 'nama'))
                                            ->searchable(),

                                        Select::make('Jalur_PMB')
                                            ->label('Jalur PMB')
                                            ->relationship('jalurPmbRef', 'nilai', function ($query) {
                                                return $query->where('nama_grup', 'jalur_pmb')
                                                    ->where('status', 1);
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->disabled(fn() => auth()->user()->isMurid()),

                                        Select::make('Jenis_Pembiayaan')
                                            ->label('Jenis Pembiayaan')
                                            ->options([
                                                'Mandiri' => 'Mandiri',
                                                'Beasiswa' => 'Beasiswa',
                                                'Lainnya' => 'Lainnya',
                                            ])
                                            ->disabled(fn() => auth()->user()->isMurid()),
                                    ])
                                    ->columns(2),
                            ]),

                        // Tab 2: Data Transfer
                        Tabs\Tab::make('Data Transfer')
                            ->schema([
                                Section::make('Data Kampus Asal')
                                    ->schema([
                                        TextInput::make('NIMKO_Asal')
                                            ->label('NIM Asal'),

                                        TextInput::make('PT_Asal')
                                            ->label('Perguruan Tinggi Asal'),

                                        TextInput::make('Prodi_Asal')
                                            ->label('Prodi Asal'),

                                        TextInput::make('Jml_SKS_Asal')
                                            ->label('Jumlah SKS Diakui')
                                            ->numeric(),

                                        TextInput::make('IPK_Asal')
                                            ->label('IPK Terakhir'),

                                        TextInput::make('Semester_Asal')
                                            ->label('Semester Asal'),
                                    ])
                                    ->columns(2),
                            ]),

                        // Tab 3: Dokumen
                        Tabs\Tab::make('Dokumen')
                            ->schema([
                                Section::make('Upload Dokumen')
                                    ->schema([
                                        FileUpload::make('Legalisir_Ijazah')
                                            ->label('Legalisir Ijazah')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'Legalisir_Ijazah'))
                                            ->visibility('public')
                                            ->preserveFilenames()
                                            ->maxSize(10240)
                                            ->downloadable()
                                            ->openable()
                                            // Hapus file saat klik ❌
                                            ->afterStateUpdated(function ($state, $record) {
                                                if (blank($state) && $record?->foto_profil) {
                                                    Storage::disk('public')->delete($record->foto_profil);
                                                }
                                            })

                                            // Hapus file lama saat upload baru
                                            ->deleteUploadedFileUsing(function ($file, $record) {
                                                if ($record?->foto_profil) {
                                                    Storage::disk('public')->delete($record->foto_profil);
                                                }
                                                return true;
                                            }), // full width,,

                                        FileUpload::make('Legalisir_SKHU')
                                            ->label('Legalisir SKHU')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPath($record, 'Legalisir_SKHU', 'siswa', $get))
                                            ->visibility('public')
                                            ->preserveFilenames()
                                            ->maxSize(10240)
                                            ->downloadable()
                                            ->openable()
                                            // Hapus file saat klik ❌
                                            ->afterStateUpdated(function ($state, $record) {
                                                if (blank($state) && $record?->foto_profil) {
                                                    Storage::disk('public')->delete($record->foto_profil);
                                                }
                                            })

                                            // Hapus file lama saat upload baru
                                            ->deleteUploadedFileUsing(function ($file, $record) {
                                                if ($record?->foto_profil) {
                                                    Storage::disk('public')->delete($record->foto_profil);
                                                }
                                                return true;
                                            }), // full width,,

                                        FileUpload::make('Copy_KTP')
                                            ->label('Copy KTP')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPath($record, 'Copy_KTP', 'siswa', $get))
                                            ->visibility('public')
                                            ->preserveFilenames()
                                            ->maxSize(10240)
                                            ->downloadable()
                                            ->openable()
                                            // Hapus file saat klik ❌
                                            ->afterStateUpdated(function ($state, $record) {
                                                if (blank($state) && $record?->foto_profil) {
                                                    Storage::disk('public')->delete($record->foto_profil);
                                                }
                                            })

                                            // Hapus file lama saat upload baru
                                            ->deleteUploadedFileUsing(function ($file, $record) {
                                                if ($record?->foto_profil) {
                                                    Storage::disk('public')->delete($record->foto_profil);
                                                }
                                                return true;
                                            }), // full width,,

                                        FileUpload::make('File_Foto_Berwarna')
                                            ->label('Pas Foto Berwarna')
                                            ->image()
                                            ->disk('public')
                                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPath($record, 'File_Foto_Berwarna', 'siswa', $get))
                                            ->visibility('public')
                                            ->preserveFilenames()
                                            ->maxSize(10240)
                                            ->downloadable()
                                            ->openable()
                                            // Hapus file saat klik ❌
                                            ->afterStateUpdated(function ($state, $record) {
                                                if (blank($state) && $record?->foto_profil) {
                                                    Storage::disk('public')->delete($record->foto_profil);
                                                }
                                            })

                                            // Hapus file lama saat upload baru
                                            ->deleteUploadedFileUsing(function ($file, $record) {
                                                if ($record?->foto_profil) {
                                                    Storage::disk('public')->delete($record->foto_profil);
                                                }
                                                return true;
                                            }), // full width,,
                                    ])
                                    ->columns(2),
                            ]),

                        // Tab 4: Status & Validasi
                        Tabs\Tab::make('Status & Validasi')
                            ->schema([
                                Section::make('Status Pendaftaran')
                                    ->schema([
                                        Select::make('status_valid')
                                            ->label('Status Validasi')
                                            ->options([
                                                '0' => 'Belum Divalidasi',
                                                '1' => 'Sudah Divalidasi',
                                            ])
                                            ->default('0')
                                            ->required()
                                            ->disabled(fn() => auth()->user()->isMurid()),

                                        Select::make('Status_Pendaftaran')
                                            ->label('Status Pendaftaran')
                                            ->options([
                                                'B' => '⏳ Pending/Proses',
                                                'Y' => '✅ Diterima',
                                                'N' => '❌ Ditolak',
                                            ])
                                            ->default('B')
                                            ->required()
                                            ->disabled(fn() => auth()->user()->isMurid()),

                                        Select::make('Status_Kelulusan')
                                            ->label('Status Kelulusan')
                                            ->options([
                                                'B' => '⏳ Proses',
                                                'Y' => '✅ Lulus',
                                                'N' => '❌ Tidak Lulus',
                                            ])
                                            ->default('B')
                                            ->required()
                                            ->disabled(fn() => auth()->user()->isMurid()),

                                        TextInput::make('Diterima_di_Prodi')
                                            ->label('Diterima di Prodi'),

                                        TextInput::make('verifikator')
                                            ->label('Verifikator'),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
