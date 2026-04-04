<?php

namespace App\Filament\Resources\SarprasPeminjamen\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;

class SarprasPeminjamanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sarpras_barang_id')
                    ->relationship('barang', 'nama_barang')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Peminjam')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->default(auth()->id())
                    ->disabled(fn () => !auth()->user()->isAdmin())
                    ->dehydrated(),
                TextInput::make('jumlah_pinjam')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1),
                DateTimePicker::make('tanggal_pinjam')
                    ->required()
                    ->default(now()),
                DateTimePicker::make('estimasi_kembali')
                    ->required()
                    ->default(now()->addDays(3)),
                Select::make('status')
                    ->options([
                        'Diajukan' => 'Diajukan',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                        'Dipinjam' => 'Dipinjam',
                        'Dikembalikan' => 'Dikembalikan',
                        'Telat' => 'Telat',
                    ])
                    ->required()
                    ->default('Diajukan')
                    ->disabled(fn () => !auth()->user()->isAdmin()),
                Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }
}
