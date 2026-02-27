<?php

namespace App\Filament\Resources\LibraryAuthor;

use App\Models\LibraryAuthor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class LibraryAuthorResource extends Resource
{
    protected static ?string $model = LibraryAuthor::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';
    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static ?string $navigationLabel = 'Penulis';
    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            Textarea::make('bio'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('books_count')->counts('books')->label('Jumlah Buku'),
        ])->actions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\LibraryAuthor\Pages\ListLibraryAuthor::route('/'),
        ];
    }
}
