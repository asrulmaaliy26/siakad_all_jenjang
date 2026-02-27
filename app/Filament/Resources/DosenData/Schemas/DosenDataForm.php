<?php

namespace App\Filament\Resources\DosenData\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use App\Models\Jurusan;
use App\Models\RefOption\Agama;
use App\Models\RefOption\PangkatGolongan;
use App\Models\RefOption\JabatanFungsional;
use App\Models\RefOption\StatusDosen;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;

class DosenDataForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Identitas')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                FileUpload::make('foto_profil')
                                    ->label('Foto Profil')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadDosenPath($record, 'foto_profil', $get))
                                    ->columnSpanFull(),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('nama')
                                            ->label('Nama Lengkap')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('id_staff')
                                            ->label('ID Staff (Legacy)')
                                            ->maxLength(255),
                                        TextInput::make('NIPDN')
                                            ->label('NIPDN')
                                            ->maxLength(50),
                                        TextInput::make('NIY')
                                            ->label('NIY')
                                            ->maxLength(50),
                                        TextInput::make('gelar_depan')
                                            ->label('Gelar Depan')
                                            ->maxLength(50),
                                        TextInput::make('gelar_belakang')
                                            ->label('Gelar Belakang')
                                            ->maxLength(50),
                                    ]),
                            ]),

                        Tabs\Tab::make('Data Pribadi')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        DatePicker::make('tanggal_lahir')
                                            ->label('Tanggal Lahir'),
                                        Select::make('jenis_kelamin')
                                            ->label('Jenis Kelamin')
                                            ->options([
                                                'L' => 'Laki-laki',
                                                'P' => 'Perempuan',
                                            ]),
                                        TextInput::make('ibu_kandung')
                                            ->label('Nama Ibu Kandung')
                                            ->maxLength(255),
                                        TextInput::make('kewarganegaraan')
                                            ->label('Kewarganegaraan')
                                            ->maxLength(100),
                                        Select::make('status_kawin')
                                            ->label('Status Kawin')
                                            ->options([
                                                'Y' => 'Sudah Kawin',
                                                'N' => 'Belum Kawin',
                                            ]),
                                        Select::make('ro_agama')
                                            ->label('Agama')
                                            ->options(Agama::pluck('nilai', 'id'))
                                            ->searchable(),
                                        Textarea::make('Alamat')
                                            ->label('Alamat')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Kepegawaian & Kontak')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(255),
                                        Select::make('id_jurusan')
                                            ->label('Jurusan')
                                            ->options(Jurusan::pluck('nama', 'id'))
                                            ->required()
                                            ->searchable(),
                                        Select::make('ro_pangkat_gol')
                                            ->label('Pangkat Golongan')
                                            ->options(PangkatGolongan::pluck('nilai', 'id'))
                                            ->searchable(),
                                        Select::make('ro_jabatan')
                                            ->label('Jabatan Fungsional')
                                            ->options(JabatanFungsional::pluck('nilai', 'id'))
                                            ->searchable(),
                                        Select::make('ro_status_dosen')
                                            ->label('Status Dosen')
                                            ->options(StatusDosen::pluck('nilai', 'id'))
                                            ->searchable(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Akun Login')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Section::make('Akun Terhubung')
                                    ->description('Dosen ini sudah memiliki akun login. Anda dapat memperbarui informasinya di bawah.')
                                    ->schema([
                                        TextInput::make('user_name')
                                            ->label('Nama Akun / Username')
                                            ->maxLength(255)
                                            ->afterStateHydrated(fn($set, $record) => $set('user_name', $record?->user?->name)),

                                        TextInput::make('user_email')
                                            ->label('Email Login')
                                            ->email()
                                            ->maxLength(255)
                                            ->afterStateHydrated(fn($set, $record) => $set('user_email', $record?->user?->email)),

                                        TextInput::make('user_password')
                                            ->label('Password Baru')
                                            ->password()
                                            ->revealable()
                                            ->placeholder('Biarkan kosong jika tidak ingin ganti password'),
                                    ])
                                    ->visible(fn($record) => $record?->user_id !== null)
                                    ->columns(1),

                                Section::make('Buat Akun Login (Pengajar)')
                                    ->description('Dosen ini belum memiliki akun login. Isi data di bawah untuk membuat akun login secara otomatis.')
                                    ->schema([
                                        TextInput::make('username_account')
                                            ->label('Username')
                                            ->placeholder('Auto-generate dari Nama jika kosong'),

                                        TextInput::make('email_account')
                                            ->label('Email')
                                            ->email()
                                            ->required()
                                            ->placeholder('Isi email untuk akun login'),

                                        TextInput::make('password_account')
                                            ->label('Password')
                                            ->password()
                                            ->revealable()
                                            ->required()
                                            ->placeholder('Masukkan password'),
                                    ])
                                    ->visible(fn($record) => $record === null || $record->user_id === null)
                                    ->columns(1)
                                    ->collapsible(),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }
}
