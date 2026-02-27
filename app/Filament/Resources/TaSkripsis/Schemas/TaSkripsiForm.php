<?php

namespace App\Filament\Resources\TaSkripsis\Schemas;

use App\Helpers\UploadPathHelper;
use App\Models\DosenData;
use App\Models\RiwayatPendidikan;
use App\Models\TahunAkademik;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TaSkripsiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Status Progress Skripsi')
                    ->compact()
                    ->schema([
                        ToggleButtons::make('status')
                            ->label('Status Saat Ini')
                            ->options([
                                'pending'   => 'Pending',
                                'disetujui' => 'Disetujui',
                                'ditolak'   => 'Ditolak',
                                'revisi'    => 'Perlu Revisi',
                                'selesai'   => 'Selesai',
                            ])
                            ->colors([
                                'pending'   => 'gray',
                                'disetujui' => 'success',
                                'ditolak'   => 'danger',
                                'revisi'    => 'warning',
                                'selesai'   => 'info',
                            ])
                            ->icons([
                                'pending'   => 'heroicon-m-clock',
                                'disetujui' => 'heroicon-m-check-circle',
                                'ditolak'   => 'heroicon-m-x-circle',
                                'revisi'    => 'heroicon-m-arrow-path',
                                'selesai'   => 'heroicon-m-flag',
                            ])
                            ->inline()
                            ->default('pending')
                            ->required()
                            ->disabled(fn() => ($user = Auth::user()) instanceof User && ($user->isPengajar() || $user->isMurid()))
                            ->dehydrated(),
                    ]),
                Tabs::make('Form Skripsi')
                    ->tabs([
                        // Tab 1: Informasi & Jadwal
                        Tabs\Tab::make('Informasi & Jadwal')
                            ->schema([
                                // ── INFORMASI UTAMA ──────────────────────────────────────────
                                Section::make('Informasi Skripsi')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('id_tahun_akademik')
                                            ->label('Tahun Akademik')
                                            ->options(TahunAkademik::all()->mapWithKeys(fn($t) => [$t->id => $t->nama . ' - ' . $t->periode]))
                                            ->searchable()
                                            ->default(fn() => \App\Models\TahunAkademik::latest('id')->value('id'))
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && ($user->isPengajar() || $user->isMurid()))
                                            ->dehydrated(),

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
                                                if (($user = Auth::user()) instanceof User && $user->isMurid()) {
                                                    $siswa = \App\Models\SiswaData::where('user_id', $user->id)->first();
                                                    return $siswa?->riwayatPendidikanAktif?->id ?? \App\Models\RiwayatPendidikan::where('id_siswa', $siswa?->id)->latest('id')->value('id');
                                                }
                                                return null;
                                            })
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && ($user->isPengajar() || $user->isMurid()))
                                            ->dehydrated(),

                                        TextInput::make('judul')
                                            ->label('Judul Penelitian')
                                            ->columnSpanFull()
                                            ->maxLength(500)
                                            ->disabled(fn($record) => ($user = Auth::user()) instanceof User && ($user->isPengajar() || ($user->isMurid() && $record !== null))),

                                        Textarea::make('abstrak')
                                            ->label('Abstrak')
                                            ->columnSpanFull()
                                            ->rows(4)
                                            ->disabled(fn($record) => ($user = Auth::user()) instanceof User && ($user->isPengajar() || ($user->isMurid() && $record !== null))),

                                        DatePicker::make('tgl_pengajuan')
                                            ->label('Tanggal Pengajuan')
                                            ->default(now())
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && ($user->isPengajar() || $user->isMurid()))
                                            ->dehydrated(),

                                    ]),

                                // ── JADWAL UJIAN ───────────────────────────────────────────
                                Section::make('Jadwal Ujian')
                                    ->columns(2)
                                    ->schema([
                                        DatePicker::make('tgl_ujian')
                                            ->label('Tanggal Sidang/Ujian')
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && $user->isMurid()),

                                        Select::make('ruangan_ujian')
                                            ->label('Ruangan Ujian')
                                            ->options(\App\Models\RefOption\RuangKelas::pluck('nilai', 'nilai'))
                                            ->searchable()
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && ($user->isPengajar() || $user->isMurid())),

                                        DatePicker::make('tgl_acc_skripsi')
                                            ->label('Tanggal ACC Skripsi')
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && $user->isMurid()),

                                        FileUpload::make('file')
                                            ->label('File Proposal')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi'))
                                            ->acceptedFileTypes([
                                                'application/pdf',
                                                'application/msword',
                                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                                            ])
                                            ->columnSpanFull()
                                            ->required()
                                            ->multiple()
                                            ->disabled(fn($record) => ($user = Auth::user()) instanceof User && ($user->isPengajar() || ($user->isMurid() && $record !== null))),
                                    ]),
                            ]),

                        // Tab 2: Berkas Pendukung
                        Tabs\Tab::make('Berkas Pendukung')
                            ->schema([
                                // ── DOKUMEN PENDUKUNG ──────────────────────────────────────────
                                Section::make('Dokumen Pendukung')
                                    ->columns(2)
                                    ->schema([
                                        FileUpload::make('file_ppt')
                                            ->label('File PPT')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi/ppt'))
                                            ->multiple()
                                            ->openable()
                                            ->downloadable(),

                                        FileUpload::make('file_plagiasi')
                                            ->label('File Plagiasi (Turnitin)')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi/plagiasi'))
                                            ->multiple()
                                            ->openable()
                                            ->downloadable(),

                                        FileUpload::make('file_kwitansi')
                                            ->label('File Kwitansi Pembayaran')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi/kwitansi'))
                                            ->multiple()
                                            ->openable()
                                            ->downloadable(),

                                        FileUpload::make('file_surat')
                                            ->label('File Surat Pernyataan')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi/surat'))
                                            ->multiple()
                                            ->openable()
                                            ->downloadable(),

                                        FileUpload::make('file_ktm')
                                            ->label('File KTM')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi/ktm'))
                                            ->multiple()
                                            ->openable()
                                            ->downloadable(),

                                        FileUpload::make('file_khs')
                                            ->label('File KHS Terakhir')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi/khs'))
                                            ->multiple()
                                            ->openable()
                                            ->downloadable(),

                                        FileUpload::make('file_kartu_bimbingan')
                                            ->label('File Kartu Bimbingan')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi/bimbingan'))
                                            ->multiple()
                                            ->openable()
                                            ->downloadable(),

                                        FileUpload::make('file_sertifikat')
                                            ->label('File Sertifikat (TOEFL/Lainnya)')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi/sertifikat'))
                                            ->multiple()
                                            ->openable()
                                            ->downloadable(),

                                        FileUpload::make('file_quisioner')
                                            ->label('File Quisioner')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi/quisioner'))
                                            ->multiple()
                                            ->openable()
                                            ->downloadable(),

                                        FileUpload::make('file_lampiran')
                                            ->label('File Lampiran Lainnya')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi/lampiran'))
                                            ->multiple()
                                            ->openable()
                                            ->downloadable(),
                                    ]),
                            ]),

                        // Tab 3: Bimbingan & Nilai
                        Tabs\Tab::make('Bimbingan & Nilai')
                            ->schema([
                                Section::make('Dosen Pembimbing')
                                    ->columns(3)
                                    ->schema([
                                        Select::make('id_dosen_pembimbing_1')
                                            ->label('Pembimbing 1')
                                            ->options(DosenData::pluck('nama', 'id'))
                                            ->searchable()
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 1))
                                            ->disabled(fn($record) => ($user = Auth::user()) instanceof User && ($user->isPengajar() || ($user->isMurid() && $record !== null))),

                                        Select::make('id_dosen_pembimbing_2')
                                            ->label('Pembimbing 2')
                                            ->options(DosenData::pluck('nama', 'id'))
                                            ->searchable()
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 2))
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && ($user->isPengajar() || $user->isMurid())),

                                        Select::make('id_dosen_pembimbing_3')
                                            ->label('Pembimbing 3')
                                            ->options(DosenData::pluck('nama', 'id'))
                                            ->searchable()
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 3))
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && ($user->isPengajar() || $user->isMurid())),
                                    ]),

                                Section::make('Penilaian Dosen Pembimbing')
                                    ->columns(3)
                                    ->visible(fn($record) => ($user = Auth::user()) instanceof User && ($user->isAdmin() || ($user->isMurid() && $record !== null) || (!$user->isMurid() && (self::isVisibleForSlot($record, 1) || self::isVisibleForSlot($record, 2) || self::isVisibleForSlot($record, 3)))))
                                    ->schema([
                                        // ── SLOT DOSEN 1 ─────────────────────────────────────
                                        ToggleButtons::make('status_dosen_1')
                                            ->label('Status Dosen 1')
                                            ->options(['pending' => 'Pending', 'setuju' => 'Setuju', 'ditolak' => 'Ditolak', 'revisi' => 'Revisi'])
                                            ->colors([
                                                'pending' => 'gray',
                                                'setuju'  => 'success',
                                                'ditolak' => 'danger',
                                                'revisi'  => 'warning',
                                            ])
                                            ->icons([
                                                'pending' => 'heroicon-m-clock',
                                                'setuju'  => 'heroicon-m-check-circle',
                                                'ditolak' => 'heroicon-m-x-circle',
                                                'revisi'  => 'heroicon-m-arrow-path',
                                            ])
                                            ->inline()
                                            ->default('pending')
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 1))
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && $user->isMurid()),

                                        TextInput::make('nilai_dosen_1')
                                            ->label('Nilai Dosen 1')
                                            ->numeric()->minValue(0)->maxValue(100)
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 1))
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && $user->isMurid()),

                                        FileUpload::make('file_revisi_1')
                                            ->label('File Revisi Dosen 1')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi-revisi'))
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 1))
                                            ->multiple()
                                            ->disabled(fn($record) => in_array($record?->status_dosen_1, ['setuju', 'ditolak'])),

                                        Placeholder::make('history_dosen_1')
                                            ->label('Riwayat Diskusi')
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 1) && $record?->ctt_revisi_dosen_1)
                                            ->content(fn($record) => new HtmlString("
                                                <div class='max-h-[300px] overflow-y-auto p-4 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 shadow-sm custom-scrollbar'>
                                                    <div class='prose prose-sm dark:prose-invert max-w-none [&_p]:inline'>
                                                        {$record->ctt_revisi_dosen_1}
                                                    </div>
                                                </div>
                                            ")),

                                        RichEditor::make('ctt_revisi_dosen_1')
                                            ->label('Tambah Catatan / Balasan')
                                            ->fileAttachmentsDirectory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi-revisi'))
                                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'redo', 'undo'])
                                            ->columnSpanFull()
                                            ->placeholder('Ketik balasan atau catatan baru di sini...')
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 1) || Auth::user()?->isAdmin())
                                            ->formatStateUsing(fn() => null)
                                            ->dehydrateStateUsing(function ($state, $record) {
                                                $isActuallyEmpty = !$state || trim(strip_tags($state)) === '';
                                                if ($isActuallyEmpty) return $record?->ctt_revisi_dosen_1;

                                                $user = Auth::user();
                                                if (!$user instanceof User) return $state . '<hr>' . $record?->ctt_revisi_dosen_1;

                                                $role = $user->isPengajar() ? 'Pengajar' : ($user->isMurid() ? 'Murid' : 'Admin');
                                                $name = $user->isPengajar() ? ($user->dosenData?->nama ?? $user->name) : ($user->isMurid() ? ($user->siswaData?->nama ?? $user->name) : $user->name);

                                                $badgeColor = match ($role) {
                                                    'Pengajar' => 'background: #0ea5e9; color: white;',
                                                    'Murid'    => 'background: #10b981; color: white;',
                                                    default    => 'background: #6366f1; color: white;',
                                                };

                                                $header = "
                                                    <div style='margin-bottom: 10px; padding: 10px; border-left: 4px solid #6366f1; background: rgba(99, 102, 241, 0.05); border-radius: 4px;'>
                                                        <div style='display: flex; align-items: center; gap: 8px; margin-bottom: 5px;'>
                                                            <span style='font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; {$badgeColor}'>{$role}</span>
                                                            <span style='font-size: 12px; font-weight: bold;'>{$name}</span>
                                                            <span style='font-size: 10px; color: #94a3b8;'>(" . now()->format('d/m/Y H:i') . ")</span>
                                                        </div>
                                                        <div style='font-size: 13px; line-height: 1.5;'>{$state}</div>
                                                    </div>
                                                ";
                                                $divider = $record?->ctt_revisi_dosen_1 ? '<hr style="margin: 15px 0; border: 0; border-top: 1px dashed #e2e8f0;">' : '';
                                                return $header . $divider . $record?->ctt_revisi_dosen_1;
                                            }),

                                        // ── SLOT DOSEN 2 ─────────────────────────────────────
                                        ToggleButtons::make('status_dosen_2')
                                            ->label('Status Dosen 2')
                                            ->options(['pending' => 'Pending', 'setuju' => 'Setuju', 'ditolak' => 'Ditolak', 'revisi' => 'Revisi'])
                                            ->colors([
                                                'pending' => 'gray',
                                                'setuju'  => 'success',
                                                'ditolak' => 'danger',
                                                'revisi'  => 'warning',
                                            ])
                                            ->icons([
                                                'pending' => 'heroicon-m-clock',
                                                'setuju'  => 'heroicon-m-check-circle',
                                                'ditolak' => 'heroicon-m-x-circle',
                                                'revisi'  => 'heroicon-m-arrow-path',
                                            ])
                                            ->inline()
                                            ->default('pending')
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 2))
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && $user->isMurid()),

                                        TextInput::make('nilai_dosen_2')
                                            ->label('Nilai Dosen 2')
                                            ->numeric()->minValue(0)->maxValue(100)
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 2))
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && $user->isMurid()),

                                        FileUpload::make('file_revisi_2')
                                            ->label('File Revisi Dosen 2')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi-revisi'))
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 2))
                                            ->multiple()
                                            ->disabled(fn($record) => in_array($record?->status_dosen_2, ['setuju', 'ditolak'])),

                                        Placeholder::make('history_dosen_2')
                                            ->label('Riwayat Diskusi')
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 2) && $record?->ctt_revisi_dosen_2)
                                            ->content(fn($record) => new HtmlString("
                                                <div class='max-h-[300px] overflow-y-auto p-4 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 shadow-sm custom-scrollbar'>
                                                    <div class='prose prose-sm dark:prose-invert max-w-none [&_p]:inline'>
                                                        {$record->ctt_revisi_dosen_2}
                                                    </div>
                                                </div>
                                            ")),

                                        RichEditor::make('ctt_revisi_dosen_2')
                                            ->label('Tambah Catatan / Balasan')
                                            ->fileAttachmentsDirectory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi-revisi'))
                                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'redo', 'undo'])
                                            ->columnSpanFull()
                                            ->placeholder('Ketik balasan atau catatan baru di sini...')
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 2) || Auth::user()?->isAdmin())
                                            ->formatStateUsing(fn() => null)
                                            ->dehydrateStateUsing(function ($state, $record) {
                                                $isActuallyEmpty = !$state || trim(strip_tags($state)) === '';
                                                if ($isActuallyEmpty) return $record?->ctt_revisi_dosen_2;

                                                $user = Auth::user();
                                                if (!$user instanceof User) return $state . '<hr>' . $record?->ctt_revisi_dosen_2;

                                                $role = $user->isPengajar() ? 'Pengajar' : ($user->isMurid() ? 'Murid' : 'Admin');
                                                $name = $user->isPengajar() ? ($user->dosenData?->nama ?? $user->name) : ($user->isMurid() ? ($user->siswaData?->nama ?? $user->name) : $user->name);

                                                $badgeColor = match ($role) {
                                                    'Pengajar' => 'background: #0ea5e9; color: white;',
                                                    'Murid'    => 'background: #10b981; color: white;',
                                                    default    => 'background: #6366f1; color: white;',
                                                };

                                                $header = "
                                                    <div style='margin-bottom: 10px; padding: 10px; border-left: 4px solid #6366f1; background: rgba(99, 102, 241, 0.05); border-radius: 4px;'>
                                                        <div style='display: flex; align-items: center; gap: 8px; margin-bottom: 5px;'>
                                                            <span style='font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; {$badgeColor}'>{$role}</span>
                                                            <span style='font-size: 12px; font-weight: bold;'>{$name}</span>
                                                            <span style='font-size: 10px; color: #94a3b8;'>(" . now()->format('d/m/Y H:i') . ")</span>
                                                        </div>
                                                        <div style='font-size: 13px; line-height: 1.5;'>{$state}</div>
                                                    </div>
                                                ";
                                                $divider = $record?->ctt_revisi_dosen_2 ? '<hr style="margin: 15px 0; border: 0; border-top: 1px dashed #e2e8f0;">' : '';
                                                return $header . $divider . $record?->ctt_revisi_dosen_2;
                                            }),

                                        // ── SLOT DOSEN 3 ─────────────────────────────────────
                                        ToggleButtons::make('status_dosen_3')
                                            ->label('Status Dosen 3')
                                            ->options(['pending' => 'Pending', 'setuju' => 'Setuju', 'ditolak' => 'Ditolak', 'revisi' => 'Revisi'])
                                            ->colors([
                                                'pending' => 'gray',
                                                'setuju'  => 'success',
                                                'ditolak' => 'danger',
                                                'revisi'  => 'warning',
                                            ])
                                            ->icons([
                                                'pending' => 'heroicon-m-clock',
                                                'setuju'  => 'heroicon-m-check-circle',
                                                'ditolak' => 'heroicon-m-x-circle',
                                                'revisi'  => 'heroicon-m-arrow-path',
                                            ])
                                            ->inline()
                                            ->default('pending')
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 3))
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && $user->isMurid()),

                                        TextInput::make('nilai_dosen_3')
                                            ->label('Nilai Dosen 3')
                                            ->numeric()->minValue(0)->maxValue(100)
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 3))
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && $user->isMurid()),

                                        FileUpload::make('file_revisi_3')
                                            ->label('File Revisi Dosen 3')
                                            ->disk('public')
                                            ->directory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi-revisi'))
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 3))
                                            ->multiple()
                                            ->disabled(fn($record) => in_array($record?->status_dosen_3, ['setuju', 'ditolak'])),

                                        Placeholder::make('history_dosen_3')
                                            ->label('Riwayat Diskusi')
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 3) && $record?->ctt_revisi_dosen_3)
                                            ->content(fn($record) => new HtmlString("
                                                <div class='max-h-[300px] overflow-y-auto p-4 rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 shadow-sm custom-scrollbar'>
                                                    <div class='prose prose-sm dark:prose-invert max-w-none [&_p]:inline'>
                                                        {$record->ctt_revisi_dosen_3}
                                                    </div>
                                                </div>
                                            ")),

                                        RichEditor::make('ctt_revisi_dosen_3')
                                            ->label('Tambah Catatan / Balasan')
                                            ->fileAttachmentsDirectory(fn($get, $record) => UploadPathHelper::uploadTaPath($get, $get, 'ta-skripsi-revisi'))
                                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'redo', 'undo'])
                                            ->columnSpanFull()
                                            ->placeholder('Ketik balasan atau catatan baru di sini...')
                                            ->visible(fn($record) => self::isVisibleForSlot($record, 3) || Auth::user()?->isAdmin())
                                            ->formatStateUsing(fn() => null)
                                            ->dehydrateStateUsing(function ($state, $record) {
                                                $isActuallyEmpty = !$state || trim(strip_tags($state)) === '';
                                                if ($isActuallyEmpty) return $record?->ctt_revisi_dosen_3;

                                                $user = Auth::user();
                                                if (!$user instanceof User) return $state . '<hr>' . $record?->ctt_revisi_dosen_3;

                                                $role = $user->isPengajar() ? 'Pengajar' : ($user->isMurid() ? 'Murid' : 'Admin');
                                                $name = $user->isPengajar() ? ($user->dosenData?->nama ?? $user->name) : ($user->isMurid() ? ($user->siswaData?->nama ?? $user->name) : $user->name);

                                                $badgeColor = match ($role) {
                                                    'Pengajar' => 'background: #0ea5e9; color: white;',
                                                    'Murid'    => 'background: #10b981; color: white;',
                                                    default    => 'background: #6366f1; color: white;',
                                                };

                                                $header = "
                                                    <div style='margin-bottom: 10px; padding: 10px; border-left: 4px solid #6366f1; background: rgba(99, 102, 241, 0.05); border-radius: 4px;'>
                                                        <div style='display: flex; align-items: center; gap: 8px; margin-bottom: 5px;'>
                                                            <span style='font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; {$badgeColor}'>{$role}</span>
                                                            <span style='font-size: 12px; font-weight: bold;'>{$name}</span>
                                                            <span style='font-size: 10px; color: #94a3b8;'>(" . now()->format('d/m/Y H:i') . ")</span>
                                                        </div>
                                                        <div style='font-size: 13px; line-height: 1.5;'>{$state}</div>
                                                    </div>
                                                ";
                                                $divider = $record?->ctt_revisi_dosen_3 ? '<hr style="margin: 15px 0; border: 0; border-top: 1px dashed #e2e8f0;">' : '';
                                                return $header . $divider . $record?->ctt_revisi_dosen_3;
                                            }),

                                        // ── HASIL AKHIR ──────────────────────────────────────
                                        TextInput::make('nilai_akhir')
                                            ->label('Nilai Akhir')
                                            ->numeric()
                                            ->step(0.01)
                                            ->disabled(fn() => ($user = Auth::user()) instanceof User && $user->isAdmin()),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Cek apakah field slot-N boleh tampil untuk user yang sedang login.
     * Admin selalu true. Dosen hanya true jika ia ada di slot tersebut.
     */
    protected static function isVisibleForSlot($record, int $slot): bool
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user instanceof User) return false;

        // Admin selalu lihat semua
        if ($user->isAdmin()) {
            return true;
        }

        // Murid hanya lihat jika sudah ada record (edit mode) ATAU jika slot 1 (saat create)
        if ($user->isMurid()) {
            return $record !== null || $slot === 1;
        }

        // Jika bukan pengajar (dan sdh cek admin/murid diatas), sembunyikan
        if (!$user->isPengajar()) {
            return false;
        }

        // Pengajar: jangan tampilkan jika record belum ada (mode create)
        if (!$record) return false;

        $dosenId  = $user->getDosenId();
        $fieldMap = [
            1 => $record->id_dosen_pembimbing_1,
            2 => $record->id_dosen_pembimbing_2,
            3 => $record->id_dosen_pembimbing_3,
        ];

        return $dosenId && ($fieldMap[$slot] ?? null) == $dosenId;
    }
}
