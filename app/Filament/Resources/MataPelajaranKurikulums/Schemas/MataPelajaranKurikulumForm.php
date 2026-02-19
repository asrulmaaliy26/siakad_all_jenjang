<?php

namespace App\Filament\Resources\MataPelajaranKurikulums\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MataPelajaranKurikulumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_kurikulum')
                    ->numeric(),
                TextInput::make('id_mata_pelajaran_master')
                    ->numeric(),
                TextInput::make('semester')
                    ->numeric(),
            ]);
    }
}
