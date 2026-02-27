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

class DosenPenelitianRelationManager extends RelationManager
{
    protected static string $relationship = 'penelitian';
    protected static ?string $title = 'Penelitian';

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('judul_penelitian')
                    ->label('Judul Penelitian')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('th_penelitian')
                    ->label('Tahun')
                    ->maxLength(10),

                Forms\Components\TextInput::make('tingkat_penelitian')
                    ->label('Tingkat')
                    ->maxLength(255),

                Forms\Components\TextInput::make('dana_penelitian')
                    ->label('Dana')
                    ->maxLength(255),

                Forms\Components\FileUpload::make('lokasi_file')
                    ->label('Upload File (Raw Data)')
                    ->directory('dosen/penelitian')
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul_penelitian')
            ->columns([
                Tables\Columns\TextColumn::make('judul_penelitian')
                    ->label('Judul')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('th_penelitian')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tingkat_penelitian')
                    ->label('Tingkat')
                    ->searchable(),

                Tables\Columns\TextColumn::make('dana_penelitian')
                    ->label('Dana')
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
