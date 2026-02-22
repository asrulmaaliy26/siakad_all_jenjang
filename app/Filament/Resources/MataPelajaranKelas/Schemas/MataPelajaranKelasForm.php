<?php

namespace App\Filament\Resources\MataPelajaranKelas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;



class MataPelajaranKelasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        Select::make('id_mata_pelajaran_kurikulum')
                            ->label('Mata Kuliah')
                            ->relationship('mataPelajaranKurikulum', 'id', function (Builder $query) {
                                return $query->with('mataPelajaranMaster');
                            })
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->mataPelajaranMaster->name ?? '-')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('id_kelas')
                            ->label('Kelas')
                            ->relationship('kelas', 'id')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->semester . ' - ' . ($record->programKelas->nilai ?? '-'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('id_dosen_data')
                            ->label('Dosen Pengajar')
                            ->relationship('dosenData', 'nama')
                            ->searchable()
                            ->preload(),
                        Select::make('ro_ruang_kelas')
                            ->label('Ruang Kelas')
                            ->relationship('ruangKelas', 'nilai', function (Builder $query) {
                                return $query->where('nama_grup', 'ruang_kelas');
                            })
                            ->searchable()
                            ->preload(),
                        Select::make('ro_pelaksanaan_kelas')
                            ->label('Pelaksanaan')
                            ->relationship('pelaksanaanKelas', 'nilai', function (Builder $query) {
                                return $query->where('nama_grup', 'pelaksanaan_kelas'); // Assuming such a group exists or will be added
                            })
                            ->searchable()
                            ->preload(),
                        TextInput::make('jumlah')
                            ->label('Kapasitas / Jumlah Mahasiswa')
                            ->numeric()
                            ->default(0),
                    ]),

                Section::make('Jadwal Rutin')
                    // ->collapsed()
                    ->columns(2)
                    ->schema([
                        Select::make('hari')
                            ->options([
                                'Senin' => 'Senin',
                                'Selasa' => 'Selasa',
                                'Rabu' => 'Rabu',
                                'Kamis' => 'Kamis',
                                'Jumat' => 'Jumat',
                                'Sabtu' => 'Sabtu',
                                'Minggu' => 'Minggu',
                            ]),
                        TextInput::make('jam')
                            ->placeholder('Contoh: 08:00 - 10:00'),
                        DatePicker::make('tanggal')
                            ->label('Tanggal (Dikosongkan jika rutin)'),
                        TextInput::make('link_kelas')
                            ->label('Link Kelas Online')
                            ->url()
                            ->columnSpanFull(),
                        TextInput::make('passcode')
                            ->label('Passcode / Password Kelas'),
                    ]),

                Section::make('Ujian Tengah Semester (UTS)')
                    ->label('Jadwal UTS')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        Toggle::make('status_uts')
                            ->label('Aktifkan UTS')
                            ->onColor('success')
                            ->offColor('danger')
                            ->inline(false),
                        DateTimePicker::make('uts')
                            ->label('Waktu Pelaksanaan UTS')
                            ->visible(fn() => !auth()->user()?->isMurid()),

                        TextInput::make('ruang_uts')
                            ->label('Ruang UTS'),
                        FileUpload::make('soal_uts')
                            ->label('Upload File Soal UTS')
                            ->disk('public')
                            ->visibility('public')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadMataPelajaranKelasPath($get, $record, 'soal_uts'))
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/*'])
                            ->preserveFilenames()
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull()
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
                        RichEditor::make('ctt_soal_uts')
                            ->label('Soal UTS (Rich Text)')
                            ->columnSpanFull(),
                    ]),

                Section::make('Ujian Akhir Semester (UAS)')
                    ->label('Jadwal UAS')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        Toggle::make('status_uas')
                            ->label('Aktifkan UAS')
                            ->onColor('success')
                            ->offColor('danger')
                            ->inline(false),
                        DateTimePicker::make('uas')
                            ->label('Waktu Pelaksanaan UAS')
                            ->visible(fn() => !auth()->user()?->isMurid()),

                        TextInput::make('ruang_uas')
                            ->label('Ruang UAS'),
                        Select::make('id_pengawas')
                            ->label('Dosen Pengawas')
                            ->relationship('pengawas', 'nama')
                            ->searchable()
                            ->preload(),
                        FileUpload::make('soal_uas')
                            ->label('Upload File Soal UAS')
                            ->disk('public')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadMataPelajaranKelasPath($get, $record, 'soal_uas'))
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/*'])
                            ->preserveFilenames()
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable()

                            ->columnSpanFull()
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
                        RichEditor::make('ctt_soal_uas')
                            ->label('Soal UAS (Rich Text)')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
