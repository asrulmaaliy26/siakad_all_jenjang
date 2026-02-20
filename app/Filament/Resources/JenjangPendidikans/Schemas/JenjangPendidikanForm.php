<?php

namespace App\Filament\Resources\JenjangPendidikans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class JenjangPendidikanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama'),
                TextInput::make('deskripsi'),
                \Filament\Forms\Components\Select::make('type')
                    ->options([
                        'sekolah' => 'Sekolah',
                        'kampus' => 'Kampus',
                    ])
                    ->required(),
            ]);
    }
}
