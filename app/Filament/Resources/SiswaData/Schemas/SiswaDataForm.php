<?php

namespace App\Filament\Resources\SiswaData\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use App\Models\Jurusan;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Card;
use Filament\Schemas\Components\Section;
use App\Models\RefOption\Agama;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiswaDataForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pribadi')
                    // ->columns(2)
                    ->schema([
                        FileUpload::make('foto_profil')
                            ->label('Foto Profil')
                            ->image()
                            ->disk('public')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadSiswaDataPath($get, $record, 'foto_profil'))
                            ->visibility('public')
                            ->preserveFilenames()
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable()
                            ->reactive()

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
                            }), // full width
                        TextInput::make('nama')
                            ->label('Nama Panggilan'),
                        TextInput::make('nama_lengkap'),
                        TextInput::make('user_id')
                            ->label('User ID (Auto-Generated)')
                            ->disabled()
                            ->dehydrated(false), // Don't save this field, user_id is set in controller

                        // Fields for creating User Account
                        TextInput::make('username_account')
                            ->label('Username (Login System)')
                            ->placeholder('Isi jika ingin custom username, kosongkan untuk auto-generate dari Nama'),

                        TextInput::make('email_account')
                            ->label('Email (Login System)')
                            ->email()
                            ->placeholder('Isi jika ingin custom email, kosongkan untuk auto-generate'),

                        TextInput::make('password_account')
                            ->label('Password (Login System)')
                            ->password()
                            ->revealable()
                            ->placeholder('Default: password'),
                    ])
                    ->collapsible(),
                Tabs::make('SiswaDataTabs')
                    ->tabs([
                        Tabs\Tab::make('Data Pribadi')
                            ->columns(2)
                            ->schema([

                                Select::make('jenis_kelamin')
                                    ->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
                                Select::make('agama')
                                    ->label('Agama')
                                    ->options(Agama::pluck('nilai', 'nilai'))
                                    ->searchable(),

                                TextInput::make('kota_lahir')
                                    ->label('Kota Lahir'),

                                DatePicker::make('tanggal_lahir')
                                    ->label('Tanggal Lahir'),

                                Textarea::make('alamat')
                                    ->label('Alamat'),
                            ]),
                        Tabs\Tab::make('Alamat & Domisili')
                            ->schema([
                                TextInput::make('nomor_rumah'),
                                TextInput::make('dusun'),
                                TextInput::make('rt'),
                                TextInput::make('rw'),
                                TextInput::make('desa'),
                                TextInput::make('kecamatan'),
                                TextInput::make('kabupaten'),
                                TextInput::make('kode_pos'),
                                TextInput::make('provinsi'),
                                Textarea::make('tempat_domisili'),
                                TextInput::make('jenis_domisili'),
                                TextInput::make('no_telepon_wa'),
                            ]),
                        Tabs\Tab::make('Sekolah')
                            ->schema([
                                TextInput::make('status_asal_sekolah'),
                                TextInput::make('asal_slta'),
                                TextInput::make('jenis_slta'),
                                TextInput::make('kejuruan_slta'),
                                Textarea::make('alamat_lengkap_sekolah_asal'),
                                TextInput::make('tahun_lulus_slta'),
                                TextInput::make('nomor_seri_ijazah_slta'),
                                TextInput::make('nisn'),
                            ]),
                        Tabs\Tab::make('Lainnya')
                            ->schema([
                                TextInput::make('anak_ke'),
                                TextInput::make('jumlah_saudara'),
                                TextInput::make('penerima_kps'),
                                TextInput::make('no_kps'),
                                TextInput::make('kebutuhan_khusus'),
                                TextInput::make('kewarganegaraan'),
                                TextInput::make('kode_negara'),
                                TextInput::make('status_kawin'),
                                TextInput::make('pekerjaan'),
                                TextInput::make('biaya_ditanggung'),
                                TextInput::make('transportasi'),
                                Select::make('golongan_darah')
                                    ->options(['A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O']),
                                TextInput::make('id_pendaftaran')->disabled(),
                            ]),
                    ])
            ]);
    }
}
