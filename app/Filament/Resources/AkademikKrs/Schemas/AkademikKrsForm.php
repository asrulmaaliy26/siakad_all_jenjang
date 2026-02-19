<?php

namespace App\Filament\Resources\AkademikKrs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AkademikKrsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_riwayat_pendidikan')
                    ->relationship('riwayatPendidikan', 'nim') // Assuming 'nim' or 'nama' is the display column
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->siswaData->nama} - {$record->nim}")
                    ->label('Mahasiswa')
                    ->searchable()
                    ->preload()
                    ->required(),

                // Select::make('id_kelas')
                //     ->relationship('kelas.programKelas', 'nilai')
                //     ->label('Kelas')
                //     ->searchable()
                //     ->preload(),

                // Data KRS
                // TextInput::make('semester')
                //     ->label('Semester')
                //     ->numeric(),

                TextInput::make('jumlah_sks')
                    ->label('Jumlah SKS')
                    ->numeric(),

                DatePicker::make('tgl_krs')
                    ->label('Tanggal KRS'),

                TextInput::make('kode_tahun')
                    ->label('Kode Tahun'),

                // ENUM fields
                Select::make('status_bayar')
                    ->label('Status Bayar')
                    ->options([
                        'Y' => 'Lunas',
                        'N' => 'Belum Lunas',
                    ])
                    ->default('N'),

                Select::make('syarat_uts')
                    ->label('Syarat UTS')
                    ->options([
                        'Y' => 'Terpenuhi',
                        'N' => 'Belum',
                    ])
                    ->default('N'),

                Select::make('syarat_uas')
                    ->label('Syarat UAS')
                    ->options([
                        'Y' => 'Terpenuhi',
                        'N' => 'Belum',
                    ])
                    ->default('N'),

                Select::make('syarat_krs')
                    ->label('Syarat KRS')
                    ->options([
                        'Y' => 'Terpenuhi',
                        'N' => 'Belum',
                    ])
                    ->default('N'),

                // TextInput::make('syarat_lain')
                //     ->label('Syarat Lain'),

                Select::make('status_aktif')
                    ->label('Status Aktif')
                    ->options([
                        'Y' => 'Aktif',
                        'N' => 'Tidak Aktif',
                    ])
                    ->default('Y'),

                // Uploads
                \Filament\Forms\Components\FileUpload::make('kwitansi_krs')
                    ->label('Bukti Pembayaran / Kwitansi')
                    ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadKrsPath($get, $record, 'kwitansi_krs'))
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->preserveFilenames()
                    ->maxSize(10240)
                    ->downloadable()
                    ->openable(),

                \Filament\Forms\Components\FileUpload::make('berkas_lain')
                    ->label('Berkas Pendukung Lain')
                    ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadKrsPath($get, $record, 'berkas_lain'))
                    ->disk('public')
                    ->visibility('public')
                    ->preserveFilenames()
                    ->maxSize(10240)
                    ->downloadable()
                    ->openable(),

                // Timestamps
                DatePicker::make('created_at')
                    ->label('Dibuat')
                    ->disabled()
                    ->visibleOn('edit'),

                DatePicker::make('updated_at')
                    ->label('Diperbarui')
                    ->disabled()
                    ->visibleOn('edit'),
            ]);
    }
}
