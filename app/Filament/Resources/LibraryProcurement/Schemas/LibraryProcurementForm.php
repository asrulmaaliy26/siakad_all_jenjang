<?php

namespace App\Filament\Resources\LibraryProcurement\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LibraryProcurementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pembelian')
                    ->schema([
                        TextInput::make('reference_no')
                            ->label('No. Referensi / Invoice')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('vendor')
                            ->label('Vendor / Toko')
                            ->required(),
                        DatePicker::make('procurement_date')
                            ->label('Tanggal Pembelian')
                            ->default(now())
                            ->required(),
                        Select::make('staff_id')
                            ->label('Petugas')
                            ->relationship('staff', 'name')
                            ->default(auth()->id())
                            ->required(),
                    ])
                    ->columns(['sm' => 1, 'md' => 2]),

                Section::make('Item Pembelian')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('library_book_id')
                                    ->label('Buku')
                                    ->relationship('book', 'title')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('quantity')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required(),
                                TextInput::make('unit_price')
                                    ->label('Harga Satuan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->required(),
                            ])
                            ->columns(['sm' => 1, 'md' => 3])
                            ->defaultItems(1),
                    ]),

                Section::make('Lainnya')
                    ->schema([
                        TextInput::make('total_amount')
                            ->label('Total Biaya')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Textarea::make('notes')
                            ->label('Catatan'),
                    ])
                    ->columns(['sm' => 1, 'md' => 2]),
            ]);
    }
}
