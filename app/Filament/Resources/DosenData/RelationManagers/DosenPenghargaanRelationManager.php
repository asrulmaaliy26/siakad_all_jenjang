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

class DosenPenghargaanRelationManager extends RelationManager
{
    protected static string $relationship = 'penghargaan';
    protected static ?string $title = 'Penghargaan';

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('judul_penghargaan')
                    ->label('Judul Penghargaan')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('jenis_penghargaan')
                    ->label('Jenis')
                    ->maxLength(255),

                Forms\Components\TextInput::make('tahun_penghargaan')
                    ->label('Tahun')
                    ->maxLength(10),

                Forms\Components\TextInput::make('tingkat_penghargaan')
                    ->label('Tingkat')
                    ->maxLength(255),

                Forms\Components\FileUpload::make('lokasi_file')
                    ->label('Upload File (Raw Data)')
                    ->directory('dosen/penghargaan')
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul_penghargaan')
            ->columns([
                Tables\Columns\TextColumn::make('judul_penghargaan')
                    ->label('Judul')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('jenis_penghargaan')
                    ->label('Jenis')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tahun_penghargaan')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tingkat_penghargaan')
                    ->label('Tingkat')
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
