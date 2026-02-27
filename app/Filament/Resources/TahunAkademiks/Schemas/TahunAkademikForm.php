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
                TextInput::make('nama')
                    ->mask('9999/9999')
                    ->placeholder('2024/2025')
                    ->formatStateUsing(fn(?string $state) => $state ? explode(' ', $state)[0] : null)
                    ->dehydrateStateUsing(fn(?string $state) => $state ? explode(' ', $state)[0] : null)
                    ->required(),
                Select::make('periode')
                    ->options(['Genap' => 'Genap', 'Ganjil' => 'Ganjil']),
                Select::make('status')
                    ->options(['Y' => 'Y', 'N' => 'N']),
            ]);
    }
}
