<?php

namespace App\Filament\Resources\AbsensiSiswas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AbsensiSiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_pertemuan')
                    ->numeric(),
                TextInput::make('id_krs')
                    ->numeric(),
                Select::make('status')
                    ->options(['Hadir' => 'Hadir', 'Izin' => 'Izin', 'Sakit' => 'Sakit', 'Alpa' => 'Alpa']),
                DateTimePicker::make('waktu_absen'),
            ]);
    }
}
