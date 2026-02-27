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

class DosenRiwayatPendidikanDosenRelationManager extends RelationManager
{
    protected static string $relationship = 'riwayatPendidikanDosen';
    protected static ?string $title = 'Riwayat Pendidikan';

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('jenjang')
                    ->label('Jenjang')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('nama_pendidikan')
                    ->label('Nama Instansi/Pendidikan')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('gelar_pendidikan')
                    ->label('Gelar')
                    ->maxLength(255),

                Forms\Components\TextInput::make('th_lulus')
                    ->label('Tahun Lulus')
                    ->maxLength(10),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_pendidikan')
            ->columns([
                Tables\Columns\TextColumn::make('jenjang')
                    ->label('Jenjang')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_pendidikan')
                    ->label('Nama Instansi')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('gelar_pendidikan')
                    ->label('Gelar'),

                Tables\Columns\TextColumn::make('th_lulus')
                    ->label('Tahun Lulus')
                    ->sortable(),
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
