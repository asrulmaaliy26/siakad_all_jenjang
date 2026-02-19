<?php

namespace App\Filament\Resources\ReferenceOptions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use App\Models\ReferenceOption;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Select;

class ReferenceOptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_grup')
                    ->label('Nama Grup')
                    ->required()
                    ->maxLength(100)
                    ->helperText('Bisa gunakan nama grup yang sudah ada atau buat baru'),

                Select::make('select_nama_grup')
                    ->label('Pilih Grup yang Sudah Ada')
                    ->options(
                        fn() =>
                        ReferenceOption::query()
                            ->distinct()
                            ->pluck('nama_grup', 'nama_grup') // value => label
                            ->toArray()
                    )
                    ->searchable()
                    ->reactive() // penting agar bisa update TextInput secara real-time
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            // Set TextInput 'nama_grup' otomatis ke pilihan dropdown
                            $set('nama_grup', $state);
                        }
                    })
                    ->placeholder('Pilih grup yang sudah ada (opsional)'),  // scrollable jika banyak

                TextInput::make('kode')
                    ->label('Kode')
                    ->maxLength(50),

                TextInput::make('nilai')
                    ->label('Nilai')
                    ->required(),

                // Toggle::make('status')
                //     ->label('Status')
                //     ->onColor('success')
                //     ->offColor('danger')

                //     // saat edit → ambil dari DB
                //     ->afterStateHydrated(function (Toggle $component, $state) {
                //         $component->state($state === 'Y');
                //     })

                //     // saat simpan → konversi ke DB
                //     ->dehydrateStateUsing(fn(bool $state) => $state ? 'Y' : 'N')

                //     ->default(true),

                Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->rows(3),
            ]);
    }
}
