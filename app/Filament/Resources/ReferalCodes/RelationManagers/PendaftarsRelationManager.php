<?php

namespace App\Filament\Resources\ReferalCodes\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PendaftarsRelationManager extends RelationManager
{
    protected static string $relationship = 'pendaftars';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                TextColumn::make('No_Pendaftaran')
                    ->label('No. Daftar')
                    ->searchable(),
                TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable(),
                TextColumn::make('Tgl_Daftar')
                    ->label('Tgl Daftar')
                    ->date(),
                TextColumn::make('Status_Pendaftaran')
                    ->label('Status')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'B' => 'Pending/Proses',
                        'Y' => 'Diterima',
                        'N' => 'Ditolak',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'B' => 'warning',
                        'Y' => 'success',
                        'N' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AssociateAction::make(),
            ])
            ->recordActions([
                DissociateAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
