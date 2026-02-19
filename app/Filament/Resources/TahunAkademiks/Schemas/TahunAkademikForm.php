<?php

namespace App\Filament\Resources\TahunAkademiks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TahunAkademikForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama'),
                Select::make('periode')
                    ->options(['Genap' => 'Genap', 'Ganjil' => 'Ganjil']),
                Select::make('status')
                    ->options(['Y' => 'Y', 'N' => 'N']),
            ]);
    }
}
