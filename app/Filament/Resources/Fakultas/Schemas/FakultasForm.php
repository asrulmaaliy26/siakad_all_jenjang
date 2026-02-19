<?php

namespace App\Filament\Resources\Fakultas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FakultasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama'),
            ]);
    }
}
