<?php

namespace App\Filament\Resources\Jurusans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class JurusanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama'),
                \Filament\Forms\Components\Select::make('id_fakultas')
                    ->relationship('fakultas', 'nama')
                    ->required(),
                \Filament\Forms\Components\Select::make('id_jenjang_pendidikan')
                    ->relationship('jenjangPendidikan', 'nama')
                    ->required(),
            ]);
    }
}
