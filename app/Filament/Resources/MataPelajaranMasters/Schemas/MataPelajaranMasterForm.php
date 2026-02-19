<?php

namespace App\Filament\Resources\MataPelajaranMasters\Schemas;

use App\Models\Jurusan;
use App\Models\RefOption\JenisMapel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MataPelajaranMasterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                Select::make('id_jurusan')
                    ->label('Jurusan')
                    ->options(Jurusan::pluck('nama', 'id'))
                    ->searchable(),
                TextInput::make('bobot')
                    ->numeric(),
                Select::make('ro_jenis')
                    ->label('Jenis Pelajaran')
                    ->options(JenisMapel::pluck('nilai', 'id'))
                    ->searchable(),
            ]);
    }
}
