<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verified_at')
                    ->dateTime(),
                TextEntry::make('view_password')
                    ->label('Password Asli')
                    ->visible(function () {
                        /** @var \App\Models\User|null $user */
                        $user = auth()->user();
                        return $user ? $user->hasRole('super_admin') : false;
                    }),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
