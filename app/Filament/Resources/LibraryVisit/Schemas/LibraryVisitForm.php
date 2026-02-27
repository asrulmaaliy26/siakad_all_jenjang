<?php

namespace App\Filament\Resources\LibraryVisit\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LibraryVisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('riwayat_pendidikan_id')
                    ->label('Mahasiswa')
                    ->relationship('riwayatPendidikan', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->siswaData->nama} - {$record->nomor_induk}")
                    ->searchable()
                    ->preload()
                    ->required(),
                DateTimePicker::make('visited_at')
                    ->label('Waktu Kunjungan')
                    ->default(now())
                    ->required(),
                TextInput::make('purpose')
                    ->label('Keperluan')
                    ->placeholder('Contoh: Membaca, Pinjam Buku, Tugas'),
            ]);
    }
}
