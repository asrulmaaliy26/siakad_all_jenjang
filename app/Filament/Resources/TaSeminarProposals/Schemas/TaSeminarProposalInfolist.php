<?php

namespace App\Filament\Resources\TaSeminarProposals\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaSeminarProposalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Seminar')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tahunAkademik.nama')->label('Tahun Akademik'),
                        TextEntry::make('riwayatPendidikan.siswa.nama')->label('Mahasiswa'),
                        TextEntry::make('riwayatPendidikan.nomor_induk')->label('NIM'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'disetujui' => 'success',
                                'ditolak'   => 'danger',
                                'revisi'    => 'warning',
                                'selesai'   => 'info',
                                default     => 'gray',
                            }),
                        TextEntry::make('judul')->label('Judul')->columnSpanFull(),
                        TextEntry::make('abstrak')->label('Abstrak')->columnSpanFull(),
                        TextEntry::make('tgl_pengajuan')->label('Tanggal Pengajuan')->date('d M Y'),
                        TextEntry::make('tgl_acc_judul')->label('Tanggal ACC')->date('d M Y'),
                    ]),

                Section::make('Jadwal & Hasil')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('tgl_ujian')->label('Tanggal Seminar')->date('d M Y'),
                        TextEntry::make('ruangan_ujian')->label('Ruangan'),
                        TextEntry::make('status_ujian')->label('Hasil Keseluruhan')->badge(),
                        TextEntry::make('nilai_rata_rata')->label('Nilai Rata-rata'),
                    ]),

                Section::make('Pembimbing')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('dosenPembimbing1.nama')->label('Pembimbing 1'),
                        TextEntry::make('dosenPembimbing2.nama')->label('Pembimbing 2'),
                        TextEntry::make('dosenPembimbing3.nama')->label('Pembimbing 3'),
                    ]),

                Section::make('Penilaian per Dosen')
                    ->columns(3)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('status_dosen_1')->label('Status Dosen 1')->badge(),
                        TextEntry::make('status_dosen_2')->label('Status Dosen 2')->badge(),
                        TextEntry::make('status_dosen_3')->label('Status Dosen 3')->badge(),
                        TextEntry::make('nilai_dosen_1')->label('Nilai Dosen 1'),
                        TextEntry::make('nilai_dosen_2')->label('Nilai Dosen 2'),
                        TextEntry::make('nilai_dosen_3')->label('Nilai Dosen 3'),
                        TextEntry::make('ctt_revisi_dosen_1')->label('Catatan Dosen 1'),
                        TextEntry::make('ctt_revisi_dosen_2')->label('Catatan Dosen 2'),
                        TextEntry::make('ctt_revisi_dosen_3')->label('Catatan Dosen 3'),
                    ]),
            ]);
    }
}
