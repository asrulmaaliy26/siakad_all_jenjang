<?php

namespace App\Filament\Resources\ReferalCodes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReferalCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required(),
                TextInput::make('kode')
                    ->label('Kode Referal (Kosongkan agar terisi otomatis)')
                    ->disabledOn('edit')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Textarea::make('keterangan')
                    ->columnSpanFull(),
                Select::make('type')
                    ->options(['internal' => 'Internal', 'eksternal' => 'Eksternal'])
                    ->default('internal')
                    ->required(),
                Select::make('status')
                    ->options([
                        'Mahasiswa' => 'Mahasiswa',
                        'Dosen' => 'Dosen',
                        'Staff IT' => 'Staff IT',
                        'Staff' => 'Staff',
                        'Mitra' => 'Mitra / Eksternal',
                        'Lainnya' => 'Lainnya'
                    ])
                    ->label('Peran / Status')
                    ->required(),
            ]);
    }
}
