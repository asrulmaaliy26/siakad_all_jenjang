<?php

namespace App\Filament\Resources\Kelas\Schemas;

use App\Models\TahunAkademik;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KelasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ro_program_kelas')
                    ->label('Program Kelas')
                    ->relationship('programKelas', 'nilai')
                    ->multiple(fn($livewire) => $livewire instanceof \App\Filament\Resources\Kelas\Pages\CreateKelas)
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('semester')
                    ->options(array_combine(range(1, 8), range(1, 8)))
                    ->multiple(fn($livewire) => $livewire instanceof \App\Filament\Resources\Kelas\Pages\CreateKelas)
                    ->required(),
                Select::make('id_tahun_akademik')
                    ->label('Tahun Akademik')
                    ->relationship('tahunAkademik', 'nama')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama} - {$record->periode}")
                    ->searchable()
                    ->preload(),
                Select::make('id_jurusan')
                    ->label('Jurusan')
                    ->relationship('jurusan', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status_aktif')
                    ->options(['Y' => 'Y', 'N' => 'N']),
                TextInput::make('kode_pddikti')
                    ->label('Kode Kelas (PDDIKTI)')
                    ->helperText('Contoh: SI2A')
                    ->maxLength(255),
            ]);
    }
}
