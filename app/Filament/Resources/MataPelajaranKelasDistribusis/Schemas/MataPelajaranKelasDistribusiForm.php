<?php

namespace App\Filament\Resources\MataPelajaranKelasDistribusis\Schemas;

use App\Models\RefOption\Hari;
use App\Models\RefOption\PelaksanaanKelas;
use App\Models\RefOption\RuangKelas;
use Filament\Forms\Components\DatePicker;
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

                Select::make('id_mata_pelajaran_kurikulum')
                    ->label('Mata Pelajaran (Kurikulum)')
                    ->relationship(
                        'mataPelajaranKurikulum.mataPelajaranMaster',
                        'nama'
                    )
                    ->searchable()
                    ->preload(),

                Select::make('id_kelas')
                    ->label('Kelas')
                    ->relationship('kelas', 'id')
                    ->getOptionLabelFromRecordUsing(
                        fn($record) =>
                        $record->id . ' - ' . optional($record->programKelas)->nilai . ' - ' . optional($record->tahunAkademik)->nama
                    )
                    // ->getOptionLabelFromRecordUsing(
                    //     fn($record) =>
                    //     $record->programKelas->nilai ?? '—'
                    // )
                    ->searchable()
                    ->preload(),

                Select::make('id_dosen_data')
                    ->label('Dosen')
                    ->relationship('dosen', 'nama')
                    ->searchable()
                    ->preload(),

                // Select::make('id_pengawas')
                //     ->label('Pengawas')
                //     ->relationship('pengawas', 'nama')
                //     ->searchable()
                //     ->preload(),

                Select::make('ro_ruang_kelas')
                    ->label('Ruang Kelas')
                    ->options(
                        RuangKelas::query()->pluck('nilai', 'id')
                    )
                    ->searchable()
                    ->preload(),

                Select::make('ro_pelaksanaan_kelas')
                    ->label('Pelaksanaan Kelas')
                    ->options(
                        PelaksanaanKelas::query()->pluck('nilai', 'id')
                    )
                    ->searchable(),

                // TextInput::make('jumlah')
                //     ->label('Jumlah Peserta')
                //     ->numeric(),

                // TextInput::make('hari')
                //     ->label('Hari'),
                Select::make('hari')
                    ->label('hari')
                    ->options(
                        Hari::query()->pluck('nilai', 'nilai') // Tetap nilai karena kolom hari di DB adalah varchar
                    )
                    ->searchable(),

                DatePicker::make('tanggal')
                    ->label('Tanggal'),

                TextInput::make('jam')
                    ->label('Jam')
                    ->placeholder('08:00 - 09:40'),

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
                            // ->relationship('ruangKelas', 'nilai')
                            ->options(
                                RuangKelas::query()->pluck('nilai', 'nilai')
                            )
                            ->searchable()
                            ->preload(),

                        Select::make('ruang_uas')
                            ->label('Ruang UAS')
                            // ->relationship('ruangKelas', 'nilai')
                            ->options(
                                RuangKelas::query()->pluck('nilai', 'nilai')
                            )
                            ->searchable()
                            ->preload(),
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
                            ->password()
                            ->columnSpan(1),
                    ]),
                // Select::make('ro_ruang_kelas')
                //     ->label('Ruang Kelas')
                //     ->options(RuangKelas::pluck('nilai', 'id'))
                //     ->searchable(),
            ]);
    }
}
