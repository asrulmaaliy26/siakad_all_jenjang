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
                TextInput::make('kode_prodi')
                    ->label('Kode Prodi (PDDIKTI)')
                    ->maxLength(255),
                \Filament\Forms\Components\Select::make('id_fakultas')
                    ->relationship('fakultas', 'nama')
                    ->required(),
            ]);
    }
}
