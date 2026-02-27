<?php

namespace App\Filament\Resources\LibraryBook\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LibraryBookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Buku')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('isbn')
                            ->label('ISBN')
                            ->unique(ignoreRecord: true),
                    ])
                    ->columns(['sm' => 1, 'md' => 2]),

                Section::make('Detail Buku')
                    ->schema([
                        Select::make('library_author_id')
                            ->label('Penulis')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required(),
                            ]),
                        Select::make('library_publisher_id')
                            ->label('Penerbit')
                            ->relationship('publisher', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('library_category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('year')
                            ->label('Tahun Terbit')
                            ->numeric(),
                    ])
                    ->columns(['sm' => 1, 'md' => 2]),

                Section::make('Inventaris')
                    ->schema([
                        TextInput::make('stock')
                            ->label('Stok Saat Ini')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        TextInput::make('location')
                            ->label('Lokasi Rak/Lemari')
                            ->placeholder('Contoh: Rak A-1'),
                        FileUpload::make('cover_image')
                            ->label('Sampul Buku')
                            ->image()
                            ->directory('library/covers')
                            ->maxSize(2048),
                    ])
                    ->columns(['sm' => 1, 'md' => 2]),
            ]);
    }
}
