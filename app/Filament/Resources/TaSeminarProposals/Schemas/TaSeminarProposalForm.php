<?php

namespace App\Filament\Resources\TaSeminarProposals\Schemas;

use App\Models\DosenData;
use App\Models\RiwayatPendidikan;
use App\Models\TahunAkademik;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaSeminarProposalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Informasi Seminar Proposal')
                    ->columns(2)
                    ->schema([
                        Select::make('id_tahun_akademik')
                            ->label('Tahun Akademik')
                            ->options(TahunAkademik::pluck('nama', 'id'))
                            ->searchable()
                            ->required(),

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
                            ->required(),

                        TextInput::make('judul')
                            ->label('Judul Penelitian')
                            ->columnSpanFull()
                            ->maxLength(500)
                            ->required(),

                        Textarea::make('abstrak')
                            ->label('Abstrak')
                            ->columnSpanFull()
                            ->rows(4),

                        DatePicker::make('tgl_pengajuan')
                            ->label('Tanggal Pengajuan')
                            ->required(),

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
                            ->required(),
                    ]),

                Section::make('Jadwal Seminar')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('tgl_ujian')
                            ->label('Tanggal Seminar'),

                        TextInput::make('ruangan_ujian')
                            ->label('Ruangan')
                            ->maxLength(50),

                        DatePicker::make('tgl_acc_judul')
                            ->label('Tanggal ACC Judul'),

                        FileUpload::make('file')
                            ->label('File Proposal')
                            ->disk('public')
                            ->directory('ta/seminar-proposal')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Dosen Pembimbing')
                    ->columns(3)
                    ->schema([
                        Select::make('id_dosen_pembimbing_1')
                            ->label('Pembimbing 1')
                            ->options(DosenData::pluck('nama', 'id'))
                            ->searchable(),

                        Select::make('id_dosen_pembimbing_2')
                            ->label('Pembimbing 2')
                            ->options(DosenData::pluck('nama', 'id'))
                            ->searchable(),

                        Select::make('id_dosen_pembimbing_3')
                            ->label('Pembimbing 3')
                            ->options(DosenData::pluck('nama', 'id'))
                            ->searchable(),
                    ]),

                Section::make('Penilaian Dosen')
                    ->columns(3)
                    ->collapsed()
                    ->schema([
                        Select::make('status_dosen_1')
                            ->label('Status Dosen 1')
                            ->options(['pending' => 'Pending', 'lulus' => 'Lulus', 'tidak_lulus' => 'Tidak Lulus', 'revisi' => 'Revisi'])
                            ->default('pending'),

                        Select::make('status_dosen_2')
                            ->label('Status Dosen 2')
                            ->options(['pending' => 'Pending', 'lulus' => 'Lulus', 'tidak_lulus' => 'Tidak Lulus', 'revisi' => 'Revisi'])
                            ->default('pending'),

                        Select::make('status_dosen_3')
                            ->label('Status Dosen 3')
                            ->options(['pending' => 'Pending', 'lulus' => 'Lulus', 'tidak_lulus' => 'Tidak Lulus', 'revisi' => 'Revisi'])
                            ->default('pending'),

                        TextInput::make('nilai_dosen_1')->label('Nilai Dosen 1')->numeric()->minValue(0)->maxValue(100),
                        TextInput::make('nilai_dosen_2')->label('Nilai Dosen 2')->numeric()->minValue(0)->maxValue(100),
                        TextInput::make('nilai_dosen_3')->label('Nilai Dosen 3')->numeric()->minValue(0)->maxValue(100),

                        FileUpload::make('file_revisi_1')->label('File Revisi Dosen 1')->disk('public')->directory('ta/revisi'),
                        FileUpload::make('file_revisi_2')->label('File Revisi Dosen 2')->disk('public')->directory('ta/revisi'),
                        FileUpload::make('file_revisi_3')->label('File Revisi Dosen 3')->disk('public')->directory('ta/revisi'),

                        Textarea::make('ctt_revisi_dosen_1')->label('Catatan Dosen 1')->rows(2),
                        Textarea::make('ctt_revisi_dosen_2')->label('Catatan Dosen 2')->rows(2),
                        Textarea::make('ctt_revisi_dosen_3')->label('Catatan Dosen 3')->rows(2),
                    ]),
            ]);
    }
}
