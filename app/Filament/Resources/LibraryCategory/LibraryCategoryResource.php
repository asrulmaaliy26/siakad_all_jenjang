<?php

namespace App\Filament\Resources\LibraryCategory;

use App\Models\LibraryCategory;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class LibraryCategoryResource extends Resource
{
    protected static ?string $model = LibraryCategory::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static ?string $navigationLabel = 'Kategori Buku';
    protected static ?int $navigationSort = 12;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->unique(ignoreRecord: true),
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
            'index' => \App\Filament\Resources\LibraryCategory\Pages\ListLibraryCategory::route('/'),
        ];
    }
}
