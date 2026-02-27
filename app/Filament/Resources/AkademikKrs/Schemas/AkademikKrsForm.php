<?php

namespace App\Filament\Resources\AkademikKrs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class AkademikKrsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi KRS')
                    ->schema([
                        Select::make('id_riwayat_pendidikan')
                            ->relationship('riwayatPendidikan', 'nim') // Assuming 'nim' or 'nama' is the display column
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->siswaData->nama} - {$record->nim}")
                            ->label('Mahasiswa')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn() => ! auth()->user()?->isMurid() || auth()->user()?->isPengajar())
                            ->columnSpanFull(),

                        TextInput::make('jumlah_sks')
                            ->label('Jumlah SKS')
                            ->numeric()
                            ->disabled(fn() => auth()->user()?->isMurid()),

                        DatePicker::make('tgl_krs')
                            ->label('Tanggal KRS')
                            ->disabled(fn() => auth()->user()?->isMurid()),

                        Select::make('kode_tahun')
                            ->label('Tahun Akademik')
                            ->options(\App\Models\TahunAkademik::all()->mapWithKeys(fn($item) => [$item->nama => "{$item->nama} - {$item->periode}"]))
                            ->default(\App\Models\TahunAkademik::where('status', 'Aktif')->first()?->nama)
                            ->searchable()
                            ->required()
                            ->disabled(fn() => auth()->user()?->isMurid()),
                    ])
                    ->columns(['sm' => 1, 'md' => 2]),

                Section::make('Persyaratan & Status')
                    ->schema([
                        Select::make('status_bayar')
                            ->label('Status Bayar')
                            ->options([
                                'Y' => 'Lunas',
                                'N' => 'Belum Lunas',
                            ])
                            ->default('N')
                            ->disabled(fn() => auth()->user()?->isMurid() || auth()->user()?->isPengajar()),

                        Select::make('syarat_uts')
                            ->label('Syarat UTS')
                            ->options([
                                'Y' => 'Terpenuhi',
                                'N' => 'Belum',
                            ])
                            ->default('N')
                            ->disabled(fn() => auth()->user()?->isMurid() || auth()->user()?->isPengajar()),

                        Select::make('syarat_uas')
                            ->label('Syarat UAS')
                            ->options([
                                'Y' => 'Terpenuhi',
                                'N' => 'Belum',
                            ])
                            ->default('N')
                            ->disabled(fn() => auth()->user()?->isMurid() || auth()->user()?->isPengajar()),

                        Select::make('syarat_krs')
                            ->label('Syarat KRS')
                            ->options([
                                'Y' => 'Terpenuhi',
                                'N' => 'Belum',
                            ])
                            ->default('N')
                            ->disabled(fn() => auth()->user()?->isMurid() || auth()->user()?->isPengajar()),
                    ])
                    ->columns(['sm' => 1, 'md' => 2]),

                Section::make('Lampiran / Berkas')
                    ->schema([
                        \Filament\Forms\Components\FileUpload::make('kwitansi_krs')
                            ->label('Bukti Pembayaran / Kwitansi')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadKrsPath($get, $record, 'kwitansi_krs'))
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable(),

                        \Filament\Forms\Components\FileUpload::make('berkas_lain')
                            ->label('Berkas Pendukung Lain')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadKrsPath($get, $record, 'berkas_lain'))
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->disk('public')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable(),
                    ])
                    ->columns(['sm' => 1, 'md' => 2]),

                Section::make('Audit Trail')
                    ->schema([
                        DatePicker::make('created_at')
                            ->label('Dibuat')
                            ->disabled(),
                        DatePicker::make('updated_at')
                            ->label('Diperbarui')
                            ->disabled(),
                    ])
                    ->columns(['sm' => 1, 'md' => 2])
                    ->visibleOn('edit'),
            ]);
    }
}
