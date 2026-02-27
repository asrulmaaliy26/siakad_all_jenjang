<?php

namespace App\Filament\Resources\PeriodeWisudas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PeriodeWisudaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tahun')
                    ->required(),
                TextInput::make('periode_ke')
                    ->required()
                    ->numeric(),
                TextInput::make('kuota')
                    ->required()
                    ->numeric()
                    ->default(800),
                TextInput::make('pendaftar_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->options(['Buka' => 'Buka', 'Tutup' => 'Tutup', 'Belum Dibuka' => 'Belum dibuka'])
                    ->default('Belum Dibuka')
                    ->required(),
                DatePicker::make('tanggal_pelaksanaan'),
            ]);
    }
}
