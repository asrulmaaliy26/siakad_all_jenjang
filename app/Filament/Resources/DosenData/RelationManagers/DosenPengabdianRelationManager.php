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

class DosenPengabdianRelationManager extends RelationManager
{
    protected static string $relationship = 'pengabdian';
    protected static ?string $title = 'Pengabdian';

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('judul_pengabdian')
                    ->label('Judul Pengabdian')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('tahun_pengabdian')
                    ->label('Tahun')
                    ->maxLength(10),

                Forms\Components\TextInput::make('tingkat_pengabdian')
                    ->label('Tingkat')
                    ->maxLength(255),

                Forms\Components\TextInput::make('dana_pengabdian')
                    ->label('Dana')
                    ->maxLength(255),

                Forms\Components\FileUpload::make('lokasi_file')
                    ->label('Upload File (Raw Data)')
                    ->directory('dosen/pengabdian')
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul_pengabdian')
            ->columns([
                Tables\Columns\TextColumn::make('judul_pengabdian')
                    ->label('Judul')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('tahun_pengabdian')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tingkat_pengabdian')
                    ->label('Tingkat')
                    ->searchable(),

                Tables\Columns\TextColumn::make('dana_pengabdian')
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
