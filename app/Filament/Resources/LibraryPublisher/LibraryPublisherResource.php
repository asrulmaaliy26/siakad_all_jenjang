<?php

namespace App\Filament\Resources\LibraryPublisher;

use App\Models\LibraryPublisher;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class LibraryPublisherResource extends Resource
{
    protected static ?string $model = LibraryPublisher::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';
    protected static string | UnitEnum | null $navigationGroup = 'Perpustakaan';
    protected static ?string $navigationLabel = 'Penerbit';
    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            Textarea::make('address'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('address')->limit(50),
        ])->actions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\LibraryPublisher\Pages\ListLibraryPublisher::route('/'),
        ];
    }
}
