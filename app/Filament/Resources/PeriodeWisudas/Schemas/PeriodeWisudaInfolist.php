<?php

namespace App\Filament\Resources\PeriodeWisudas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PeriodeWisudaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tahun'),
                TextEntry::make('periode_ke')
                    ->numeric(),
                TextEntry::make('kuota')
                    ->numeric(),
                TextEntry::make('pendaftar_count')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('tanggal_pelaksanaan')
                    ->date(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
