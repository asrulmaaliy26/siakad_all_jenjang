<?php

namespace App\Filament\Resources\WisudaMahasiswas\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WisudaMahasiswaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id_riwayat_pendidikan')
                    ->numeric(),
                IconEntry::make('bebas_prodi')
                    ->boolean(),
                IconEntry::make('bebas_fakultas')
                    ->boolean(),
                IconEntry::make('bebas_perpustakaan')
                    ->boolean(),
                IconEntry::make('bebas_keuangan')
                    ->boolean(),
                TextEntry::make('nama_arab'),
                TextEntry::make('tempat_lahir_arab'),
                TextEntry::make('no_hp'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('pas_foto'),
                TextEntry::make('id_pembimbing_1')
                    ->numeric(),
                TextEntry::make('id_pembimbing_2')
                    ->numeric(),
                TextEntry::make('id_periode_wisuda')
                    ->numeric(),
                TextEntry::make('status_pendaftaran'),
                TextEntry::make('tanggal_daftar')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
