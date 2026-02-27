<?php

namespace App\Filament\Resources\LibraryLoan\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LibraryLoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Peminjaman')
                    ->schema([
                        Select::make('riwayat_pendidikan_id')
                            ->label('Mahasiswa')
                            ->relationship('riwayatPendidikan', 'id')
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->siswaData->nama} - {$record->nomor_induk}")
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('staff_id')
                            ->label('Petugas')
                            ->relationship('staff', 'name')
                            ->default(auth()->id())
                            ->disabled(),
                        DatePicker::make('borrowed_at')
                            ->label('Tanggal Pinjam')
                            ->default(now())
                            ->required(),
                        DatePicker::make('due_at')
                            ->label('Batas Kembali')
                            ->default(now()->addDays(7))
                            ->required(),
                    ])
                    ->columns(['sm' => 1, 'md' => 2]),

                Section::make('Daftar Buku')
                    ->schema([
                        Select::make('books')
                            ->label('Pilih Buku')
                            ->relationship('books', 'title')
                            ->multiple()
                            ->preload()
                            ->required()
                            ->helperText('Satu peminjaman bisa mencakup beberapa buku.'),
                    ]),

                Section::make('Status Pengembalian')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'borrowed' => 'Dipinjam',
                                'returned' => 'Dikembalikan',
                                'overdue' => 'Terlambat',
                                'lost' => 'Hilang',
                            ])
                            ->default('borrowed')
                            ->required(),
                        DatePicker::make('returned_at')
                            ->label('Tanggal Kembali'),
                        TextInput::make('fine_amount')
                            ->label('Denda')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                    ])
                    ->columns(['sm' => 1, 'md' => 3])
                    ->visibleOn('edit'),
            ]);
    }
}
