<?php

namespace App\Filament\Resources\SiswaDataOrangTuas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiswaDataOrangTuaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama'),
                TextInput::make('id_siswa_data')
                    ->numeric(),
            ]);
    }
}
