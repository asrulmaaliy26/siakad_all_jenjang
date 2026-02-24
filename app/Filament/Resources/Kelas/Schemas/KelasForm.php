<?php

namespace App\Filament\Resources\Kelas\Schemas;

use App\Models\JenjangPendidikan;
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
                    ->options(\App\Models\RefOption\ProgramKelas::pluck('nilai', 'id'))
                    ->searchable()
                    ->required(),
                // TextInput::make('nama'),
                // ->numeric(),
                TextInput::make('semester'),
                Select::make('id_tahun_akademik')
                    ->label('Tahun Akademik')
                    ->options(TahunAkademik::all()->mapWithKeys(fn($item) => [$item->id => "{$item->nama} - {$item->periode}"]))
                    ->searchable(),
                Select::make('id_jurusan')
                    ->label('Jurusan')
                    ->options(\App\Models\Jurusan::pluck('nama', 'id'))
                    ->searchable()
                    ->required(),
                // Select::make('id_jenjang_pendidikan') // Removed as per request
                //     ->label('Jenjang Pendidikan')
                //     ->options(JenjangPendidikan::pluck('nama', 'id'))
                //     ->searchable(),
                Select::make('status_aktif')
                    ->options(['Y' => 'Y', 'N' => 'N']),
            ]);
    }
}
