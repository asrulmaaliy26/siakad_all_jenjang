<?php

namespace App\Filament\Resources\Kurikulums\Schemas;

use App\Models\JenjangPendidikan;
use App\Models\Jurusan;
use App\Models\TahunAkademik;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KurikulumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama'),
                Select::make('id_jurusan')
                    ->label('Jurusan')
                    ->options(Jurusan::pluck('nama', 'id'))
                    ->searchable(),
                Select::make('id_tahun_akademik')
                    ->label('Tahun Akademik')
                    ->options(TahunAkademik::all()->mapWithKeys(fn($item) => [$item->id => "{$item->nama} - {$item->periode}"]))
                    ->searchable(),
                // Select::make('id_jenjang_pendidikan') // Removed as per request
                //     ->label('Jenjang Pendidikan')
                //     ->options(JenjangPendidikan::pluck('nama', 'id'))
                //     ->searchable(),
                Select::make('status_aktif')
                    ->options(['Y' => 'Y', 'N' => 'N']),
            ]);
    }
}
