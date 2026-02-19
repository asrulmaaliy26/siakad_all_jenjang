<?php

namespace App\Filament\Resources\PekanUjians\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class PekanUjianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pekan Ujian')
                    ->schema([
                        Select::make('id_tahun_akademik')
                            ->label('Tahun Akademik')
                            ->relationship('tahunAkademik', 'nama')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nama} - {$record->periode}"),

                        Select::make('jenis_ujian')
                            ->label('Jenis Ujian')
                            ->options([
                                'UTS' => 'Ujian Tengah Semester (UTS)',
                                'UAS' => 'Ujian Akhir Semester (UAS)',
                            ])
                            ->required(),

                        Select::make('status_akses')
                            ->label('Status Akses')
                            ->options([
                                'Y' => 'Dibuka',
                                'N' => 'Ditutup',
                            ])
                            ->required(),

                        Select::make('status_bayar')
                            ->label('Status Syarat Pembayaran')
                            ->options([
                                'Y' => 'Wajib Lunas',
                                'N' => 'Bebas / Tidak Wajib',
                            ])
                            ->required(),

                        Select::make('status_ujian')
                            ->label('Status Aktif')
                            ->options([
                                'Y' => 'Aktif',
                                'N' => 'Tidak Aktif',
                            ])
                            ->required(),

                        Textarea::make('informasi')
                            ->label('Informasi Tambahan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
