<?php

namespace App\Filament\Resources\SiswaDataPendaftars\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;

class SiswaDataPendaftarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Status Pendaftar')
                    ->schema([
                        Select::make('status_valid')
                            ->label('Status Validasi')
                            ->options([
                                '0' => 'Belum Divalidasi',
                                '1' => 'Sudah Divalidasi',
                            ])
                            ->default('0')
                            ->required()
                            ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),

                        Select::make('Status_Pendaftaran')
                            ->label('Status Pendaftaran')
                            ->options([
                                'B' => '⏳ Pending/Proses',
                                'Y' => '✅ Diterima',
                                'N' => '❌ Ditolak',
                            ])
                            ->default('B')
                            ->required()
                            ->rules([
                                fn($get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    if ($value === 'Y') {
                                        if ($get('status_valid') !== '1' || $get('Status_Kelulusan_Seleksi') !== 'Y') {
                                            $fail('Tidak dapat diterima: Pastikan Status Validasi "Sudah Divalidasi" dan Kelulusan Seleksi "Lulus".');
                                        }
                                    }
                                },
                            ])
                            ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),

                        Select::make('Status_Kelulusan_Seleksi')
                            ->label('Status Kelulusan Seleksi')
                            ->options([
                                'B' => '⏳ Proses',
                                'Y' => '✅ Lulus',
                                'N' => '❌ Tidak Lulus',
                            ])
                            ->default('B')
                            ->required()
                            ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Tabs::make('Data Pendaftar')
                    ->tabs([
                        // Tab 1: Data Dasar
                        Tabs\Tab::make('Data Dasar')
                            ->schema([
                                Section::make('Informasi Pendaftaran')
                                    ->schema([
                                        \Filament\Forms\Components\Toggle::make('is_new_siswa')
                                            ->label('Registrasi Siswa Baru (Buat Akun & Profil Sekaligus)')
                                            ->live()
                                            ->default(true)
                                            ->visible(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                                            ->columnSpanFull(),

                                        Select::make('id_siswa_data')
                                            ->label('Pilih Siswa (Bila Sudah Ada)')
                                            ->relationship('siswa', 'nama')
                                            ->searchable()
                                            ->preload()
                                            ->visible(fn($livewire, $get) => ($livewire instanceof \Filament\Resources\Pages\CreateRecord) && !$get('is_new_siswa'))
                                            ->required(fn($livewire, $get) => ($livewire instanceof \Filament\Resources\Pages\CreateRecord) && !$get('is_new_siswa'))
                                            ->columnSpanFull(),

                                        Group::make()
                                            ->schema([
                                                Section::make('Data Akun Login')
                                                    ->schema([
                                                        TextInput::make('new_nama')
                                                            ->label('Nama Lengkap')
                                                            ->required(fn($get) => $get('is_new_siswa')),
                                                        TextInput::make('new_username')
                                                            ->label('Username')
                                                            ->unique('users', 'email')
                                                            ->required(fn($get) => $get('is_new_siswa')),
                                                        TextInput::make('new_password')
                                                            ->label('Password')
                                                            ->password()
                                                            ->required(fn($get) => $get('is_new_siswa')),
                                                    ])->columns(3),

                                                Section::make('Data Profil Dasar')
                                                    ->schema([
                                                        Select::make('new_jenis_kelamin')
                                                            ->label('Jenis Kelamin')
                                                            ->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
                                                        TextInput::make('new_tempat_lahir')
                                                            ->label('Tempat Lahir'),
                                                        DatePicker::make('new_tanggal_lahir')
                                                            ->label('Tanggal Lahir'),
                                                        TextInput::make('new_no_telepon')
                                                            ->label('Nomor Telepon/WA'),
                                                        \Filament\Forms\Components\Textarea::make('new_alamat')
                                                            ->label('Alamat Lengkap')
                                                            ->columnSpanFull(),
                                                    ])->columns(2),
                                            ])
                                            ->visible(fn($livewire, $get) => ($livewire instanceof \Filament\Resources\Pages\CreateRecord) && $get('is_new_siswa'))
                                            ->columnSpanFull(),

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
                                            ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),

                                        Select::make('id_tahun_akademik')
                                            ->label('Tahun Akademik')
                                            ->relationship('tahunAkademik', 'nama')
                                            ->default(fn() => \App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id)
                                            ->required()
                                            ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),


                                        DatePicker::make('Tgl_Daftar')
                                            ->label('Tanggal Daftar')
                                            ->default(now())
                                            ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),

                                        Select::make('id_referal_code')
                                            ->label('Pilih Referral')
                                            ->relationship('referalCode', 'nama')
                                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nama} ({$record->kode})")
                                            ->searchable(['nama', 'kode'])
                                            ->preload()
                                            ->nullable(),
                                    ])
                                    ->columns(2),

                                Section::make('Program Studi')
                                    ->schema([
                                        Select::make('ro_program_sekolah')
                                            ->label('Program Sekolah')
                                            ->relationship('programSekolahRef', 'nilai', function ($query) {
                                                return $query->where('nama_grup', 'program_sekolah')
                                                    ->where('status', 'Y');
                                            })
                                            ->searchable()
                                            ->required()
                                            ->preload()
                                            ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),
                                        TextInput::make('Kelas_Program_Kuliah')
                                            ->label('Kelas Program'),

                                        Select::make('id_jurusan')
                                            ->label('Jurusan')
                                            ->relationship('jurusan', 'nama')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),


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
                                                    ->where('status', 'Y');
                                            })
                                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nilai} - {$record->deskripsi}")
                                            ->searchable()
                                            ->required()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function ($state, $set) {
                                                if (!$state) {
                                                    $set('Biaya_Pendaftaran', 0);
                                                    return;
                                                }
                                                $ref = \App\Models\ReferenceOption::find($state);
                                                if ($ref && preg_match('/Rp\.\s*([\d.]+)/', $ref->deskripsi, $matches)) {
                                                    $value = (int) str_replace('.', '', $matches[1]);
                                                    $set('Biaya_Pendaftaran', $value);
                                                }
                                            })
                                            ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),

                                        TextInput::make('Biaya_Pendaftaran')
                                            ->label('Biaya Pendaftaran')
                                            ->prefix('Rp')
                                            ->numeric()
                                            ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),

                                        Select::make('Jenis_Pembiayaan')
                                            ->label('Jenis Pembiayaan')
                                            ->options([
                                                'Mandiri' => 'Mandiri',
                                                'Orang Tua' => 'Orang Tua',
                                                'Beasiswa' => 'Beasiswa',
                                                'Lainnya' => 'Lainnya',
                                            ])
                                            ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),
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
                                            ->multiple()
                                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'Legalisir_Ijazah'))
                                            ->visibility('public')
                                            ->preserveFilenames()
                                            ->maxSize(10240)
                                            ->downloadable()
                                            ->openable(),

                                        FileUpload::make('Legalisir_SKHU')
                                            ->label('Legalisir SKHU')
                                            ->disk('public')
                                            ->multiple()
                                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'Legalisir_SKHU'))
                                            ->visibility('public')
                                            ->preserveFilenames()
                                            ->maxSize(10240)
                                            ->downloadable()
                                            ->openable(),

                                        FileUpload::make('Copy_KTP')
                                            ->label('Copy KTP')
                                            ->disk('public')
                                            ->multiple()
                                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'Copy_KTP'))
                                            ->visibility('public')
                                            ->preserveFilenames()
                                            ->maxSize(10240)
                                            ->downloadable()
                                            ->openable(),

                                        FileUpload::make('Foto_BW_3x3')
                                            ->label('Foto B/W 3x3')
                                            ->image()
                                            ->disk('public')
                                            ->multiple()
                                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'Foto_BW_3x3'))
                                            ->visibility('public')
                                            ->preserveFilenames()
                                            ->maxSize(10240)
                                            ->downloadable()
                                            ->openable(),

                                        FileUpload::make('Foto_BW_3x4')
                                            ->label('Foto B/W 3x4')
                                            ->image()
                                            ->disk('public')
                                            ->multiple()
                                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'Foto_BW_3x4'))
                                            ->visibility('public')
                                            ->preserveFilenames()
                                            ->maxSize(10240)
                                            ->downloadable()
                                            ->openable(),

                                        FileUpload::make('Foto_Warna_5x6')
                                            ->label('Foto Warna 5x6')
                                            ->image()
                                            ->disk('public')
                                            ->multiple()
                                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'Foto_Warna_5x6'))
                                            ->visibility('public')
                                            ->preserveFilenames()
                                            ->maxSize(10240)
                                            ->downloadable()
                                            ->openable(),

                                        FileUpload::make('File_Foto_Berwarna')
                                            ->label('Pas Foto Berwarna')
                                            ->image()
                                            ->disk('public')
                                            ->multiple()
                                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'File_Foto_Berwarna'))
                                            ->visibility('public')
                                            ->preserveFilenames()
                                            ->maxSize(10240)
                                            ->downloadable()
                                            ->openable(),
                                    ])
                                    ->columns(2),
                            ]),

                        // Tab 4: Status & Validasi
                        Tabs\Tab::make('Status & Validasi')
                            ->schema([
                                Section::make('Status Pendaftaran')
                                    ->schema([
                                        TextInput::make('Diterima_di_Prodi')
                                            ->label('Diterima di Prodi'),

                                        TextInput::make('verifikator')
                                            ->label('Verifikator'),
                                    ])
                                    ->columns(2),
                            ]),
                        Tabs\Tab::make('Program Seleksi')
                            ->hidden(function ($record) {
                                /** @var \App\Models\User|null $user */
                                $user = \Illuminate\Support\Facades\Auth::user();
                                return $record && $record->Status_Pendaftaran === 'Y' && $user?->isMurid();
                            })
                            ->badge(fn($record) => $record?->seleksi()->count() ?: null)
                            ->badgeColor(fn($record) => $record?->seleksi()->count() > 0 ? 'warning' : 'primary')
                            ->schema([
                                Section::make('Riwayat & Jadwal Seleksi Mahasiswa')
                                    ->description('Admin dapat mengatur jadwal seleksi dan Mahasiswa dapat mengunggah file yang diperlukan di sini.')
                                    ->schema([
                                        // Placeholder::make('peringatan_info')
                                        //     ->label('')
                                        //     ->content(new \Illuminate\Support\HtmlString('<div style="color: #ea580c; background-color: #fff7ed; padding: 12px; border-radius: 8px; border: 1px solid #fdba74; margin-bottom: 0.5rem;"><strong style="font-size: 1.1em;">⚠️ Perhatian:</strong> Tahap seleksi yang dibuat dan disimpan di sistem akan langsung muncul di Panel Mahasiswa.</div>'))
                                        //     ->columnSpanFull(),
                                        Repeater::make('seleksi')
                                            ->relationship('seleksi')
                                            ->schema([
                                                TextInput::make('nama_seleksi')
                                                    ->label('Nama Tahap Seleksi')
                                                    ->placeholder('Contoh: Tes Tulis, Wawancara, atau Portofolio')
                                                    ->required()
                                                    ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),

                                                DateTimePicker::make('tanggal_seleksi')
                                                    ->label('Waktu Seleksi')
                                                    ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),

                                                \Filament\Forms\Components\RichEditor::make('deskripsi_seleksi')
                                                    ->label('Petunjuk / Persyaratan')
                                                    ->placeholder('Tuliskan petunjuk pengerjaan atau persyaratan jurnal/seleksi di sini...')
                                                    ->columnSpanFull()
                                                    ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),

                                                FileUpload::make('file_persyaratan')
                                                    ->label('File Persyaratan/Soal (Disediakan Kampus - Bisa Multi Upload)')
                                                    ->disk('public')
                                                    ->directory(fn($get, $record, $livewire) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $livewire->record, 'file_persyaratan_seleksi'))
                                                    ->visibility('public')
                                                    ->multiple()
                                                    ->maxSize(51200) // 50MB
                                                    ->columnSpanFull()
                                                    ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),

                                                Section::make('Respon Mahasiswa')
                                                    ->schema([
                                                        FileUpload::make('file_jawaban')
                                                            ->label('Upload Jawaban / Bukti (Bisa Upload Video, Gambar, atau Dokumen Banyak Sekaligus)')
                                                            ->disk('public')
                                                            ->directory(fn($get, $record, $livewire) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $livewire->record, 'file_jawaban_seleksi'))
                                                            ->visibility('public')
                                                            ->multiple()
                                                            ->maxSize(102400) // 100MB
                                                            ->acceptedFileTypes(['application/pdf', 'image/*', 'video/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/x-rar-compressed', 'text/plain'])
                                                            ->columnSpanFull(),
                                                    ])->compact(),

                                                Group::make()
                                                    ->schema([
                                                        TextInput::make('nilai')
                                                            ->label('Nilai')
                                                            ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),
                                                        Select::make('status_seleksi')
                                                            ->label('Status Hasil')
                                                            ->options([
                                                                'B' => '⏳ Belum Dinilai',
                                                                'Y' => '✅ Lulus / Sesuai',
                                                                'N' => '❌ Tidak Lulus',
                                                            ])
                                                            ->default('B')
                                                            ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),
                                                        \Filament\Forms\Components\RichEditor::make('keterangan_admin')
                                                            ->label('Catatan Admin')
                                                            ->columnSpanFull()
                                                            ->disabled(fn() => (\Illuminate\Support\Facades\Auth::user()?->isMurid() || \Illuminate\Support\Facades\Auth::user()?->isPendaftar())),
                                                    ])->columns(2),
                                            ])
                                            ->columnSpanFull()
                                            ->reorderable(false)
                                            ->addActionLabel('Tambah Tahap Seleksi')
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
