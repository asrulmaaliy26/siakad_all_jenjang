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
use Illuminate\Support\Facades\Storage;

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
                                if ($user && $user->isPengajar()) {
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
                        ...array_map(fn($i) => TextInput::make("Nilai_TGS_{$i}")->numeric()->maxValue(100)->label("Nilai TGS $i"), range(1, 12)),
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
                            ->multiple()
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $get, 'ljk_uts'))
                            ->visibility('public')
                            ->downloadable()
                            ->openable(), // full width,,
                        FileUpload::make('artikel_uts')
                            ->label('Artikel UTS')
                            ->disk('public')
                            ->multiple()
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $get, 'artikel_uts'))
                            ->visibility('public'), // full width,,
                        DatePicker::make('tgl_upload_ljk_uts')->label('Tgl Upload LJK UTS'),
                        RichEditor::make('ctt_uts')
                            ->label('Catatan UTS')
                            ->fileAttachmentsDirectory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $get, 'ljk_uts')),
                    ]),

                Section::make('Berkas UAS')
                    ->collapsed()
                    ->schema([
                        FileUpload::make('ljk_uas')
                            ->label('Lembar Jawab UAS')
                            ->disk('public')
                            ->multiple()
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $get, 'ljk_uas'))
                            ->visibility('public')
                            ->downloadable()
                            ->openable(), // full width,,
                        FileUpload::make('artikel_uas')
                            ->label('Artikel UAS')
                            ->disk('public')
                            ->multiple()
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $get, 'artikel_uas'))
                            ->visibility('public'), // full width,,
                        DatePicker::make('tgl_upload_ljk_uas')->label('Tgl Upload LJK UAS'),
                        DatePicker::make('tgl_upload_artikel_uas')->label('Tgl Upload Artikel UAS'),
                        RichEditor::make('ctt_uas')
                            ->label('Catatan UAS')
                            ->fileAttachmentsDirectory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $get, 'ljk_uas')),
                    ]),

                ...array_map(fn($i) => Section::make("Tugas {$i}")
                    ->collapsed()
                    ->schema([
                        FileUpload::make("ljk_tugas_{$i}")
                            ->label("File Tugas {$i}")
                            ->disk('public')
                            ->multiple()
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadTugasPath($get, $get, (string)$i))
                            ->visibility('public')
                            ->downloadable()
                            ->openable(),
                        RichEditor::make("ctt_tugas_{$i}")
                            ->label("Catatan Tugas {$i}")
                            ->fileAttachmentsDirectory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadTugasPath($get, $get, (string)$i)),
                    ]), range(1, 12)),
            ]);
    }
}
