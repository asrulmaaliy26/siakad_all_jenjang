<?php

namespace App\Filament\Resources\Ulasans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UlasanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Pengulas'),
                TextEntry::make('objek')
                    ->label('Objek Spesifik'),
                TextEntry::make('bintang')
                    ->label('Rating Bintang')
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state) . " ($state)"),
                TextEntry::make('komentar')
                    ->label('Isi Komentar')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime(),
            ]);
    }
}
