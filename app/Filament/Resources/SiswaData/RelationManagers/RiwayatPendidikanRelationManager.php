<?php

namespace App\Filament\Resources\SiswaData\RelationManagers;

use App\Models\RefOption\JenisKeluar;
use App\Models\RefOption\JenisPendaftaran;
use App\Models\RefOption\ProgramSekolah;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikanRelationManager extends RelationManager
{
    protected static string $relationship = 'riwayatPendidikan';
    protected static ?string $title = 'Riwayat Pendidikan';

    public function form(Schema $form): Schema
    {
        return $form->schema([
            // Relasi dan pilihan
            // Select::make('id_jenjang_pendidikan')
            //     ->label('Jenjang Pendidikan')
            //     ->relationship('jenjangPendidikan', 'nama'), // kolom display dari JenjangPendidikan


            Select::make('id_jurusan')
                ->label('Jurusan')
                ->relationship('jurusan', 'nama'), // kolom display dari Jurusan


            Select::make('ro_status_siswa')
                ->label('Status Siswa')
                ->relationship('statusSiswa', 'nilai'), // kolom display dari StatusSiswa


            // kalo mau pake model yang kaya di bawah harus menambahkan relation di
            // Select::make('ro_program_sekolah')
            //     ->label('Program Sekolah')
            //     ->relationship('ProgramSekolah', 'nilai')
            Select::make('ro_program_sekolah')
                ->label('Program Sekolah')
                ->options(ProgramSekolah::pluck('nilai', 'id'))
                ->searchable(),
            // Data teks
            TextInput::make('nomor_induk')
                ->label('Nomor Induk'),

            TextInput::make('angkatan')
                ->label('Angkatan'),

            TextInput::make('smt_aktif')
                ->numeric()
                ->label('Semester Aktif'),

            TextInput::make('th_masuk')
                ->label('Tahun Masuk'),

            TextInput::make('dosen_wali')
                ->label('Dosen Wali'),

            TextInput::make('no_seri_ijazah')
                ->label('No. Seri Ijazah'),

            TextInput::make('sks_diakui')
                ->label('SKS Diakui'),

            TextInput::make('judul_skripsi')
                ->label('Judul Skripsi'),

            TextInput::make('jalur_skripsi')
                ->label('Jalur Skripsi'),

            TextInput::make('bln_awal_bimbingan')
                ->label('Bulan Awal Bimbingan'),

            TextInput::make('bln_akhir_bimbingan')
                ->label('Bulan Akhir Bimbingan'),

            TextInput::make('sk_yudisium')
                ->label('SK Yudisium'),

            DatePicker::make('tgl_sk_yudisium')
                ->label('Tanggal SK Yudisium'),

            TextInput::make('ipk')
                ->label('IPK'),

            TextInput::make('nm_pt_asal')
                ->label('Nama PT Asal'),

            TextInput::make('nm_prodi_asal')
                ->label('Nama Prodi Asal'),

            Select::make('ro_jns_daftar')
                ->label('Jenis Pendaftaran')
                ->options(JenisPendaftaran::pluck('nilai', 'id'))
                ->searchable(),
            Select::make('ro_jns_keluar')
                ->label('Jenis Keluar')
                ->options(JenisKeluar::pluck('nilai', 'id'))
                ->searchable(),

            TextInput::make('keluar_smt')
                ->label('Keluar Semester'),

            TextInput::make('keterangan')
                ->label('Keterangan'),

            TextInput::make('pembiayaan')
                ->label('Pembiayaan'),

            // Select::make('status')
            //     ->label('Status')
            //     ->options([
            //         'Y' => 'Aktif',
            //         'N' => 'Tidak Aktif',
            //     ]),

            // Upload foto profil
            FileUpload::make('foto_profil')
                ->label('Foto Profil')
                ->image()
                ->disk('public')
                ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPath($record, 'foto_profil', 'siswa', $get))
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
        ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('angkatan'),
                Tables\Columns\TextColumn::make('nomor_induk'),
                Tables\Columns\TextColumn::make('jurusan.nama'),
                Tables\Columns\TextColumn::make('programSekolah.nilai'),
                Tables\Columns\TextColumn::make('statusSiswa.nilai'),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make()
                    ->using(function (Model $record, array $data): Model {

                        // Ambil data asli sebelum edit
                        $oldRecord = $record->fresh();

                        // Copy data lama untuk history
                        $history = $oldRecord->replicate();
                        $history->status = 'N';
                        $history->save();

                        // Update data baru
                        $data['status'] = 'Y';
                        $record->update($data);

                        return $record;
                    }),


                DeleteAction::make(),
            ]);
    }
    public function isReadOnly(): bool
    {
        return false;
    }
}
