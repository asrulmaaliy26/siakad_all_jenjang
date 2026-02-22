<?php

namespace App\Filament\Resources\TaPengajuanJuduls\Schemas;

use App\Models\DosenData;
use App\Models\RiwayatPendidikan;
use App\Models\TahunAkademik;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaPengajuanJudulForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // ── INFORMASI UTAMA ──────────────────────────────────────────
                Section::make('Informasi Pengajuan')
                    ->columns(2)
                    ->schema([
                        Select::make('id_tahun_akademik')
                            ->label('Tahun Akademik')
                            ->options(TahunAkademik::all()->mapWithKeys(fn($t) => [$t->id => $t->nama . ' - ' . $t->periode]))
                            ->searchable()
                            ->default(fn() => \App\Models\TahunAkademik::latest('id')->value('id'))
                            ->required()
                            ->disabled(fn() => auth()->user()?->isPengajar() || auth()->user()?->isMurid()),

                        Select::make('id_riwayat_pendidikan')
                            ->label('Mahasiswa')
                            ->options(
                                RiwayatPendidikan::with('siswa')
                                    ->get()
                                    ->mapWithKeys(fn($rp) => [
                                        $rp->id => ($rp->siswa?->nama ?? '-') . ' (' . ($rp->nomor_induk ?? 'N/A') . ')',
                                    ])
                            )
                            ->searchable()
                            ->default(function () {
                                $user = auth()->user();
                                if ($user && $user->isMurid()) {
                                    $siswa = \App\Models\SiswaData::where('user_id', $user->id)->first();
                                    return $siswa?->riwayatPendidikanAktif?->id ?? \App\Models\RiwayatPendidikan::where('id_siswa', $siswa?->id)->latest('id')->value('id');
                                }
                                return null;
                            })
                            ->required()
                            ->disabled(fn() => auth()->user()?->isPengajar() || auth()->user()?->isMurid()),

                        TextInput::make('judul')
                            ->label('Judul Penelitian')
                            ->columnSpanFull()
                            ->maxLength(500)
                            ->disabled(fn($record) => auth()->user()?->isPengajar() || (auth()->user()?->isMurid() && $record !== null)),

                        Textarea::make('abstrak')
                            ->label('Abstrak')
                            ->columnSpanFull()
                            ->rows(4)
                            ->disabled(fn($record) => auth()->user()?->isPengajar() || (auth()->user()?->isMurid() && $record !== null)),

                        DatePicker::make('tgl_pengajuan')
                            ->label('Tanggal Pengajuan')
                            ->default(now())
                            ->disabled(fn() => auth()->user()?->isPengajar() || auth()->user()?->isMurid()),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending'   => 'Pending',
                                'disetujui' => 'Disetujui',
                                'ditolak'   => 'Ditolak',
                                'revisi'    => 'Perlu Revisi',
                                'selesai'   => 'Selesai',
                            ])
                            ->default('pending')
                            ->disabled(fn() => auth()->user()?->isPengajar() || auth()->user()?->isMurid()),
                    ]),

                // ── JADWAL UJIAN ───────────────────────────────────────────
                Section::make('Jadwal Ujian')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('tgl_ujian')
                            ->label('Tanggal Sidang/Ujian')
                            ->disabled(fn() => auth()->user()?->isMurid()),

                        Select::make('ruangan_ujian')
                            ->label('Ruangan Ujian')
                            ->options(\App\Models\RefOption\RuangKelas::pluck('nilai', 'nilai'))
                            ->searchable()
                            ->disabled(fn() => auth()->user()?->isPengajar() || auth()->user()?->isMurid()),

                        DatePicker::make('tgl_acc_judul')
                            ->label('Tanggal ACC Judul')
                            ->disabled(fn() => auth()->user()?->isMurid()),

                        FileUpload::make('file')
                            ->label('File Proposal')
                            ->disk('public')
                            ->directory('ta/pengajuan-judul')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                            ])
                            ->columnSpanFull()
                            ->required()
                            ->disabled(fn($record) => auth()->user()?->isPengajar() || (auth()->user()?->isMurid() && $record !== null)),
                    ]),

                // ── PEMBIMBING ───────────────────────────────────────────────
                Section::make('Dosen Pembimbing')
                    ->columns(3)
                    ->schema([
                        // Pembimbing 1 — admin: select bebas | dosen: tampil jika dia ada di slot ini
                        Select::make('id_dosen_pembimbing_1')
                            ->label('Pembimbing 1')
                            ->options(DosenData::pluck('nama', 'id'))
                            ->searchable()
                            ->visible(fn($record) => self::isVisibleForSlot($record, 1))
                            ->disabled(fn() => auth()->user()?->isPengajar() || auth()->user()?->isMurid()),

                        // Pembimbing 2
                        Select::make('id_dosen_pembimbing_2')
                            ->label('Pembimbing 2')
                            ->options(DosenData::pluck('nama', 'id'))
                            ->searchable()
                            ->visible(fn($record) => self::isVisibleForSlot($record, 2))
                            ->disabled(fn() => auth()->user()?->isPengajar() || auth()->user()?->isMurid()),

                        // Pembimbing 3
                        Select::make('id_dosen_pembimbing_3')
                            ->label('Pembimbing 3')
                            ->options(DosenData::pluck('nama', 'id'))
                            ->searchable()
                            ->visible(fn($record) => self::isVisibleForSlot($record, 3))
                            ->disabled(fn() => auth()->user()?->isPengajar() || auth()->user()?->isMurid()),
                    ]),

                // ── PENILAIAN DOSEN ──────────────────────────────────────────
                Section::make('Penilaian Dosen Pembimbing')
                    ->columns(3)
                    // ->collapsed()
                    ->visible(fn($record) => self::isVisibleForSlot($record, 1) || self::isVisibleForSlot($record, 2) || self::isVisibleForSlot($record, 3))
                    ->schema([
                        // ── SLOT DOSEN 1 ─────────────────────────────────────
                        Select::make('status_dosen_1')
                            ->label('Status Dosen 1')
                            ->options(['pending' => 'Pending', 'setuju' => 'Setuju', 'ditolak' => 'Ditolak', 'revisi' => 'Revisi'])
                            ->default('pending')
                            ->visible(fn($record) => self::isVisibleForSlot($record, 1))
                            ->disabled(fn() => auth()->user()?->isMurid()),

                        TextInput::make('nilai_dosen_1')
                            ->label('Nilai Dosen 1')
                            ->numeric()->minValue(0)->maxValue(100)
                            ->visible(fn($record) => self::isVisibleForSlot($record, 1))
                            ->disabled(fn() => auth()->user()?->isMurid()),

                        FileUpload::make('file_revisi_dosen_1')
                            ->label('File Revisi Dosen 1')
                            ->disk('public')->directory('ta/revisi')
                            ->visible(fn($record) => self::isVisibleForSlot($record, 1))
                            ->disabled(
                                fn($record) =>
                                in_array($record?->status_dosen_1, ['setuju', 'ditolak'])
                            ),

                        RichEditor::make('ctt_revisi_dosen_1')
                            ->label('Catatan Revisi Dosen 1')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'redo', 'undo'])
                            ->columnSpanFull()
                            ->visible(fn($record) => self::isVisibleForSlot($record, 1))
                            ->disabled(fn() => auth()->user()?->isMurid()),

                        // ── SLOT DOSEN 2 ─────────────────────────────────────
                        Select::make('status_dosen_2')
                            ->label('Status Dosen 2')
                            ->options(['pending' => 'Pending', 'setuju' => 'Setuju', 'ditolak' => 'Ditolak', 'revisi' => 'Revisi'])
                            ->default('pending')
                            ->visible(fn($record) => self::isVisibleForSlot($record, 2))
                            ->disabled(fn() => auth()->user()?->isMurid()),

                        TextInput::make('nilai_dosen_2')
                            ->label('Nilai Dosen 2')
                            ->numeric()->minValue(0)->maxValue(100)
                            ->visible(fn($record) => self::isVisibleForSlot($record, 2))
                            ->disabled(fn() => auth()->user()?->isMurid()),

                        FileUpload::make('file_revisi_dosen_2')
                            ->label('File Revisi Dosen 2')
                            ->disk('public')->directory('ta/revisi')
                            ->visible(fn($record) => self::isVisibleForSlot($record, 2))
                            ->disabled(
                                fn($record) =>
                                in_array($record?->status_dosen_2, ['setuju', 'ditolak'])
                            ),

                        RichEditor::make('ctt_revisi_dosen_2')
                            ->label('Catatan Revisi Dosen 2')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'redo', 'undo'])
                            ->columnSpanFull()
                            ->visible(fn($record) => self::isVisibleForSlot($record, 2))
                            ->disabled(fn() => auth()->user()?->isMurid()),

                        // ── SLOT DOSEN 3 ─────────────────────────────────────
                        Select::make('status_dosen_3')
                            ->label('Status Dosen 3')
                            ->options(['pending' => 'Pending', 'setuju' => 'Setuju', 'ditolak' => 'Ditolak', 'revisi' => 'Revisi'])
                            ->default('pending')
                            ->visible(fn($record) => self::isVisibleForSlot($record, 3))
                            ->disabled(fn() => auth()->user()?->isMurid()),

                        TextInput::make('nilai_dosen_3')
                            ->label('Nilai Dosen 3')
                            ->numeric()->minValue(0)->maxValue(100)
                            ->visible(fn($record) => self::isVisibleForSlot($record, 3))
                            ->disabled(fn() => auth()->user()?->isMurid()),

                        FileUpload::make('file_revisi_dosen_3')
                            ->label('File Revisi Dosen 3')
                            ->disk('public')->directory('ta/revisi')
                            ->visible(fn($record) => self::isVisibleForSlot($record, 3))
                            ->disabled(
                                fn($record) =>
                                in_array($record?->status_dosen_3, ['setuju', 'ditolak'])
                            ),

                        RichEditor::make('ctt_revisi_dosen_3')
                            ->label('Catatan Revisi Dosen 3')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'redo', 'undo'])
                            ->columnSpanFull()
                            ->visible(fn($record) => self::isVisibleForSlot($record, 3))
                            ->disabled(fn() => auth()->user()?->isMurid()),
                    ]),
            ]);
    }

    /**
     * Cek apakah field slot-N boleh tampil untuk user yang sedang login.
     * Admin selalu true. Dosen hanya true jika ia ada di slot tersebut.
     */
    protected static function isVisibleForSlot($record, int $slot): bool
    {
        $user = \Filament\Facades\Filament::auth()->user();

        // Murid selalu lihat semua slot (agar bisa upload file revisi)
        if ($user && $user->isMurid()) {
            return true;
        }

        // Admin / super_admin selalu lihat semua slot
        if (!$user || !$user->isPengajar()) {
            return true;
        }

        if (!$record) return false;

        $dosenId  = \App\Models\DosenData::where('user_id', $user->id)->value('id');
        $fieldMap = [
            1 => $record->id_dosen_pembimbing_1,
            2 => $record->id_dosen_pembimbing_2,
            3 => $record->id_dosen_pembimbing_3,
        ];

        return $dosenId && ($fieldMap[$slot] ?? null) == $dosenId;
    }
}
