<?php

namespace App\Filament\Resources\SiswaDataLJKS\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Helpers\UploadPathHelper;

class SiswaDataLJKForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->columns(2)
                    ->schema([
                        Select::make('id_akademik_krs')
                            ->label('Mahasiswa (KRS)')
                            ->relationship('akademikKrs', 'id')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->riwayatPendidikan->siswa->nama . ' - ' . $record->riwayatPendidikan->nomor_induk)
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('id_mata_pelajaran_kelas')
                            ->label('Mata Kuliah')
                            ->relationship('mataPelajaranKelas', 'id', modifyQueryUsing: function ($query) {
                                $user = \Illuminate\Support\Facades\Auth::user();
                                if ($user && $user->hasRole('pengajar') && !$user->hasAnyRole(['super_admin', 'admin'])) {
                                    $query->whereHas('dosenData', function ($q) use ($user) {
                                        $q->where('user_id', $user->id);
                                    });
                                }
                            })
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->mataPelajaranKurikulum->mataPelajaranMaster->nama . ' - ' . ($record->kelas->programKelas->nilai ?? '-'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Toggle::make('cekal_kuliah')
                            ->label('Cekal Kuliah')
                            ->onColor('danger')
                            ->offColor('success')
                            ->formatStateUsing(fn($state) => $state === 'Y')
                            ->dehydrateStateUsing(fn($state) => $state ? 'Y' : 'N'),
                        Toggle::make('transfer')
                            ->label('Mahasiswa Transfer')
                            ->formatStateUsing(fn($state) => $state === 'Y')
                            ->dehydrateStateUsing(fn($state) => $state ? 'Y' : 'N'),
                    ]),

                Section::make('Nilai & Evaluasi')
                    ->columns(3)
                    ->schema([
                        TextInput::make('Nilai_UTS')->numeric()->maxValue(100)->label('Nilai UTS'),
                        TextInput::make('Nilai_UAS')->numeric()->maxValue(100)->label('Nilai UAS'),
                        TextInput::make('Nilai_TGS_1')->numeric()->maxValue(100)->label('Nilai TGS 1'),
                        TextInput::make('Nilai_TGS_2')->numeric()->maxValue(100)->label('Nilai TGS 2'),
                        TextInput::make('Nilai_TGS_3')->numeric()->maxValue(100)->label('Nilai TGS 3'),
                        TextInput::make('Nilai_Performance')->numeric()->maxValue(100)->label('Nilai Performance'),
                        TextInput::make('Nilai_Akhir')->numeric()->label('Nilai Akhir')->readOnly(), // Biasanya calculated
                        TextInput::make('Nilai_Huruf')->label('Nilai Huruf'),
                        TextInput::make('Status_Nilai')->label('Status Nilai'), // Lulus/Gagal
                        TextInput::make('Rekom_Nilai')->label('Rekomendasi'),
                        Textarea::make('ket')->label('Keterangan')->columnSpanFull(),
                    ]),

                Section::make('Berkas UTS')
                    ->collapsed()
                    ->schema([
                        FileUpload::make('ljk_uts')
                            ->label('Lembar Jawab UTS')
                            ->disk('public')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $record, 'ljk_uts'))
                            ->visibility('public')
                            ->downloadable()
                            ->openable(),
                        FileUpload::make('artikel_uts')
                            ->label('Artikel UTS')
                            ->disk('public')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $record, 'artikel_uts'))
                            ->visibility('public'),
                        DatePicker::make('tgl_upload_ljk_uts')->label('Tgl Upload LJK UTS'),
                        RichEditor::make('ctt_uts')->label('Catatan UTS'),
                    ]),

                Section::make('Berkas UAS')
                    ->collapsed()
                    ->schema([
                        FileUpload::make('ljk_uas')
                            ->label('Lembar Jawab UAS')
                            ->disk('public')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $record, 'ljk_uas'))
                            ->visibility('public')
                            ->downloadable()
                            ->openable(),
                        FileUpload::make('artikel_uas')
                            ->label('Artikel UAS')
                            ->disk('public')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $record, 'artikel_uas'))
                            ->visibility('public'),
                        DatePicker::make('tgl_upload_ljk_uas')->label('Tgl Upload LJK UAS'),
                        DatePicker::make('tgl_upload_artikel_uas')->label('Tgl Upload Artikel UAS'),
                        RichEditor::make('ctt_uas')->label('Catatan UAS'),
                    ]),

                Section::make('Tugas 1')
                    ->collapsed()
                    ->schema([
                        FileUpload::make('ljk_tugas_1')
                            ->label('File Tugas 1')
                            ->disk('public')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadTugasPath($get, $record, '1'))
                            ->visibility('public')
                            ->downloadable()
                            ->openable(),
                        RichEditor::make('ctt_tugas_1')
                            ->label('Catatan Tugas 1'),
                        // DatePicker::make('tgl_upload_tugas')->label('Tgl Upload Tugas 1'), // Assuming generic date or specific column needed
                    ]),
                Section::make('Tugas 2')
                    ->collapsed()
                    ->schema([
                        FileUpload::make('ljk_tugas_2')
                            ->label('File Tugas 2')
                            ->disk('public')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadTugasPath($get, $record, '2'))
                            ->visibility('public')
                            ->downloadable()
                            ->openable(),
                        RichEditor::make('ctt_tugas_2')
                            ->label('Catatan Tugas 2'),
                    ]),
                Section::make('Tugas 3')
                    ->collapsed()
                    ->schema([
                        FileUpload::make('ljk_tugas_3')
                            ->label('File Tugas 3')
                            ->disk('public')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadTugasPath($get, $record, '3'))
                            ->visibility('public')
                            ->downloadable()
                            ->openable(),
                        RichEditor::make('ctt_tugas_3')
                            ->label('Catatan Tugas 3'),
                    ]),
            ]);
    }
}
