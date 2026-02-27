<?php

namespace App\Filament\Resources\ReferalCodes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ReferalCodeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nama'),
                TextEntry::make('kode'),
                TextEntry::make('type'),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
