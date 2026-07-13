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
                        Select::make('tahun_akademik_filter')
                            ->label('Tahun Ajaran')
                            ->options(
                                \App\Models\TahunAkademik::orderByDesc('id')
                                    ->pluck('nama', 'id')
                            )
                            ->live()
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record && $record->mataPelajaranKelas && $record->mataPelajaranKelas->kelas) {
                                    $component->state($record->mataPelajaranKelas->kelas->id_tahun_akademik);
                                }
                            })
                            ->columnSpanFull(),
                            
                        Select::make('id_akademik_krs')
                            ->label('Mahasiswa (KRS)')
                            ->relationship('akademikKrs', 'id', modifyQueryUsing: function ($query, $get) {
                                $tahunId = $get('tahun_akademik_filter');
                                if ($tahunId) {
                                    $query->where('id_tahun_akademik', $tahunId);
                                }
                            })
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->riwayatPendidikan->siswa->nama . ' - ' . $record->riwayatPendidikan->nomor_induk)
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                            
                        Select::make('id_mata_pelajaran_kelas')
                            ->label('Mata Kuliah')
                            ->relationship('mataPelajaranKelas', 'id', modifyQueryUsing: function ($query, $get) {
                                $tahunId = $get('tahun_akademik_filter');
                                if ($tahunId) {
                                    $query->whereHas('kelas', function ($q) use ($tahunId) {
                                        $q->where('id_tahun_akademik', $tahunId);
                                    });
                                }

                                $user = \Illuminate\Support\Facades\Auth::user();
                                if ($user && $user->isPengajar()) {
                                    $query->whereHas('dosenData', function ($q) use ($user) {
                                        $q->where('user_id', $user->id);
                                    });
                                }
                            })
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->mataPelajaranKurikulum->mataPelajaranMaster->nama . ' - ' . ($record->kelas->programKelas->nilai ?? '-') . ' (' . ($record->kelas->tahunAkademik->nama ?? '-') . ')')
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
                        TextInput::make('Nilai_UTS')->numeric()->step(0.01)->minValue(0)->maxValue(4)->placeholder('0.00 - 4.00')->label('Nilai UTS'),
                        TextInput::make('Nilai_UAS')->numeric()->step(0.01)->minValue(0)->maxValue(4)->placeholder('0.00 - 4.00')->label('Nilai UAS'),
                        ...array_map(fn($i) => TextInput::make("Nilai_TGS_{$i}")->numeric()->step(0.01)->minValue(0)->maxValue(4)->placeholder('0.00 - 4.00')->label("Nilai TGS $i"), range(1, 12)),
                        TextInput::make('Nilai_Performance')->numeric()->step(0.01)->minValue(0)->maxValue(4)->placeholder('0.00 - 4.00')->label('Nilai Performance'),
                        TextInput::make('Nilai_Akhir')->numeric()->label('Nilai Akhir (IP)')->readOnly()->placeholder('Otomatis'),
                        TextInput::make('Nilai_Huruf')->label('Grade')->readOnly()->placeholder('Otomatis'),
                        Select::make('Status_Nilai')
                            ->label('Status Kelulusan')
                            ->options([
                                'LULUS' => 'LULUS',
                                'TL' => 'TIDAK LULUS',
                            ]),
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
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/*'])
                            ->downloadable()
                            ->openable(), // full width,,
                        FileUpload::make('artikel_uts')
                            ->label('Artikel UTS')
                            ->disk('public')
                            ->multiple()
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $get, 'artikel_uts'))
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/*']), // full width,,
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
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/*'])
                            ->downloadable()
                            ->openable(), // full width,,
                        FileUpload::make('artikel_uas')
                            ->label('Artikel UAS')
                            ->disk('public')
                            ->multiple()
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $get, 'artikel_uas'))
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/*']), // full width,,
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
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/*'])
                            ->downloadable()
                            ->openable(),
                        RichEditor::make("ctt_tugas_{$i}")
                            ->label("Catatan Tugas {$i}")
                            ->fileAttachmentsDirectory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadTugasPath($get, $get, (string)$i)),
                    ]), range(1, 12)),

                Section::make('Informasi Tugas Akhir / Skripsi')
                    ->description('Hanya tampil jika mahasiswa sedang menempuh Skripsi')
                    ->visible(fn($record) => $record && $record->taSkripsi()->exists())
                    ->columns(2)
                    ->schema([
                        TextInput::make('taSkripsi.judul')
                            ->label('Judul Skripsi')
                            ->disabled()
                            ->columnSpanFull(),
                        TextInput::make('taSkripsi.nilai_akhir')
                            ->label('Nilai Akhir Skripsi')
                            ->disabled(),
                        TextInput::make('taSkripsi.grade')
                            ->label('Grade Skripsi')
                            ->disabled(),
                        TextInput::make('taSkripsi.dosenPembimbing1.nama')
                            ->label('Pembimbing 1')
                            ->disabled(),
                        TextInput::make('taSkripsi.dosenPembimbing2.nama')
                            ->label('Pembimbing 2')
                            ->disabled(),
                    ]),
            ]);
    }
}
