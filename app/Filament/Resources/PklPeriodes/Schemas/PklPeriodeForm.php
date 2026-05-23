<?php

namespace App\Filament\Resources\PklPeriodes\Schemas;

use App\Models\TahunAkademik;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PklPeriodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Periode')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Periode')
                            ->placeholder('Contoh: PKL Semester Genap 2024')
                            ->required()
                            ->columnSpanFull(),
                        
                        Select::make('id_tahun_akademik')
                            ->label('Tahun Akademik')
                            ->options(TahunAkademik::all()->mapWithKeys(fn($t) => [$t->id => $t->nama]))
                            ->searchable()
                            ->required(),
                        
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->inline(false),
                        
                        DatePicker::make('tgl_mulai')
                            ->label('Tanggal Mulai Pendaftaran')
                            ->required(),
                        
                        DatePicker::make('tgl_selesai')
                            ->label('Tanggal Selesai Pendaftaran')
                            ->required(),
                    ]),
            ]);
    }
}
