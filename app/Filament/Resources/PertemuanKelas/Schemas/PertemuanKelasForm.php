<?php

namespace App\Filament\Resources\PertemuanKelas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PertemuanKelasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_mata_pelajaran_kelas')
                    ->numeric(),
                TextInput::make('pertemuan_ke')
                    ->numeric(),
                DatePicker::make('tanggal'),
                TextInput::make('materi'),
            ]);
    }
}
