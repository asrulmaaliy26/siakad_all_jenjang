<?php

namespace App\Filament\Resources\SarprasSuratKategoris\Schemas;

use Filament\Schemas\Schema;

class SarprasSuratKategoriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('format_nomor')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Contoh: {counter}/SIAKAD/SARPRAS/{kode}/{year}'),
            ]);
    }
}
