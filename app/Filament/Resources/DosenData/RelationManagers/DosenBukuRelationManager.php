<?php

namespace App\Filament\Resources\DosenData\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class DosenBukuRelationManager extends RelationManager
{
    protected static string $relationship = 'buku';
    protected static ?string $title = 'Buku';

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('id_staff')
                    ->label('ID Staff (Legacy)')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn($record) => $record?->id_staff !== null),

                Forms\Components\TextInput::make('judul_buku')
                    ->label('Judul Buku')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('tahun_buku')
                    ->label('Tahun')
                    ->maxLength(10),

                Forms\Components\TextInput::make('isbn')
                    ->label('ISBN')
                    ->maxLength(255),

                Forms\Components\TextInput::make('penerbit')
                    ->label('Penerbit')
                    ->maxLength(255),

                Forms\Components\Textarea::make('link_isbn')
                    ->label('Link ISBN')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul_buku')
            ->columns([
                Tables\Columns\TextColumn::make('judul_buku')
                    ->label('Judul')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('tahun_buku')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('isbn')
                    ->label('ISBN')
                    ->searchable(),

                Tables\Columns\TextColumn::make('penerbit')
                    ->label('Penerbit')
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
