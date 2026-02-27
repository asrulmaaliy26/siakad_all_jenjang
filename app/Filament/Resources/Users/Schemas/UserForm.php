<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn($operation) => $operation === 'create')
                    ->afterStateUpdated(function (?string $state, \Closure $set) {
                        if (filled($state)) {
                            $set('view_password', $state);
                        }
                    })
                    ->live(onBlur: true),
                \Filament\Forms\Components\Hidden::make('view_password')
                    ->dehydrated(fn($state) => filled($state)),
                \Filament\Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
