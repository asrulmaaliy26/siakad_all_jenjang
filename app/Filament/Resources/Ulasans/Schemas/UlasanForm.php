<?php

namespace App\Filament\Resources\Ulasans\Schemas;

use App\Models\User;
use App\Models\Kelas;
use App\Models\MataPelajaranMaster;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\MorphToSelect;
use Filament\Schemas\Schema;

class UlasanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Pengulas')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->default(Auth::id())
                    ->disabled(!Auth::user()?->hasRole('super_admin'))
                    ->dehydrated()
                    ->required(),

                TextInput::make('objek')
                    ->label('Objek / Tujuan Ulasan')
                    ->placeholder('Misal: Kelas 10A, Guru Budi, atau Sarana Prasarana')
                    ->maxLength(255)
                    ->required(),

                Select::make('bintang')
                    ->label('Rating Bintang')
                    ->options([
                        5 => '⭐⭐⭐⭐⭐ (5)',
                        4 => '⭐⭐⭐⭐ (4)',
                        3 => '⭐⭐⭐ (3)',
                        2 => '⭐⭐ (2)',
                        1 => '⭐ (1)',
                    ])
                    ->default(5),

                \Filament\Forms\Components\RichEditor::make('komentar')
                    ->label('Komentar / Ulasan')
                    ->placeholder('Tuliskan komentar Anda di sini...')
                    ->columnSpanFull(),
            ]);
    }
}
