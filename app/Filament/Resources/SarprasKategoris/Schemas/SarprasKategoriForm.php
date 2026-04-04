<?php

namespace App\Filament\Resources\SarprasKategoris\Schemas;

use Filament\Schemas\Schema;

class SarprasKategoriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('nama_kategori')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\Textarea::make('deskripsi')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }
}
