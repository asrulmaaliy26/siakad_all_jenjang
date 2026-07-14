<?php

namespace App\Filament\Resources\MataPelajaranKelasDistribusis\Schemas;

use App\Models\DosenData;

use App\Models\RefOption\PelaksanaanKelas;
use App\Models\RefOption\RuangKelas;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MataPelajaranKelasDistribusiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Informasi (read-only) ──────────────────────────────
                Section::make('Informasi Mata Pelajaran')
                    ->columns(2)
                    ->schema([
                        Placeholder::make('mata_pelajaran_label')
                            ->label('Mata Pelajaran')
                            ->content(fn($record) => $record?->mataPelajaranKurikulum?->mataPelajaranMaster?->nama ?? '—')
                            ->columnSpanFull(),

                        Placeholder::make('kelas_label')
                            ->label('Kelas')
                            ->content(fn($record) => $record
                                ? ($record->kelas?->programKelas?->nilai ?? '—')
                                . ' — Smt ' . ($record->kelas?->semester ?? '?')
                                . ' — ' . ($record->kelas?->tahunAkademik?->nama ?? '?')
                                . ' — ' . ($record->kelas?->jurusan?->nama ?? '?')
                                : '—'),
                    ]),

                // ── Data yang bisa diedit ──────────────────────────────
                Section::make('Pengajar & Ruang')
                    ->columns(2)
                    ->schema([
                        Select::make('id_dosen_data')
                            ->label('Dosen Pengajar')
                            ->options(fn() => DosenData::orderBy('nama')->pluck('nama', 'id')->toArray())
                            ->default(fn() => auth()->user()->getDosenId())
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('ro_ruang_kelas')
                            ->label('Ruang Kelas')
                            ->options(fn() => RuangKelas::orderBy('nilai')->pluck('nilai', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('ro_pelaksanaan_kelas')
                            ->label('Pelaksanaan Kelas')
                            ->options(fn() => PelaksanaanKelas::orderBy('nilai')->pluck('nilai', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        TextInput::make('jumlah')
                            ->label('Jumlah Peserta')
                            ->numeric()
                            ->nullable(),
                    ]),

                Section::make('Jadwal')
                    ->columns(2)
                    ->schema([
                        Select::make('hari')
                            ->label('Hari')
                            ->options([
                                'Senin'  => 'Senin',
                                'Selasa' => 'Selasa',
                                'Rabu'   => 'Rabu',
                                'Kamis'  => 'Kamis',
                                'Jumat'  => 'Jumat',
                                'Sabtu'  => 'Sabtu',
                                'Minggu' => 'Minggu',
                            ])
                            ->nullable(),

                        TextInput::make('jam')
                            ->label('Jam')
                            ->placeholder('10.00-11.40'),

                        DatePicker::make('tanggal')
                            ->label('Tanggal Mulai'),
                    ]),

                Section::make('UTS & UAS')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('tgl_uts')
                            ->label('Tanggal UTS'),

                        DatePicker::make('tgl_uas')
                            ->label('Tanggal UAS'),

                        Toggle::make('status_uts')
                            ->label('Status UTS')
                            ->inline(false)
                            ->dehydrateStateUsing(fn($state) => $state ? 'Y' : 'N')
                            ->afterStateHydrated(
                                fn($component, $state) =>
                                $component->state($state === 'Y')
                            ),

                        Toggle::make('status_uas')
                            ->label('Status UAS')
                            ->inline(false)
                            ->dehydrateStateUsing(fn($state) => $state ? 'Y' : 'N')
                            ->afterStateHydrated(
                                fn($component, $state) =>
                                $component->state($state === 'Y')
                            ),

                        Select::make('ruang_uts')
                            ->label('Ruang UTS')
                            ->options(fn() => RuangKelas::orderBy('nilai')->pluck('nilai', 'nilai')->toArray())
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('ruang_uas')
                            ->label('Ruang UAS')
                            ->options(fn() => RuangKelas::orderBy('nilai')->pluck('nilai', 'nilai')->toArray())
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ]),

                Section::make('Kelas Online')
                    ->columns(2)
                    ->schema([
                        TextInput::make('link_kelas')
                            ->label('Link Kelas')
                            ->url()
                            ->placeholder('https://')
                            ->columnSpanFull(),

                        TextInput::make('passcode')
                            ->label('Passcode')
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
