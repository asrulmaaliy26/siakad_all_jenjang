<?php

namespace App\Filament\Resources\PklPeriodes\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LembagasRelationManager extends RelationManager
{
    protected static string $relationship = 'lembagas';
    protected static ?string $title = 'Lembaga Tersedia';
    protected static ?string $recordTitleAttribute = 'nama';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kuota')
                    ->label('Kuota')
                    ->numeric()
                    ->required()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                TextColumn::make('nama')
                    ->label('Nama Lembaga'),

                TextColumn::make('kuota')
                    ->label('Kuota Total'),

                TextColumn::make('pendaftarans_count')
                    ->label('Terisi')
                    ->counts('pendaftarans'),

                TextColumn::make('sisa_kuota')
                    ->label('Sisa')
                    ->state(function ($record) {
                        $terisi = $record->pendaftarans()->where('id_pkl_periode', $this->getOwnerRecord()->id)->count();
                        return $record->pivot->kuota - $terisi;
                    }),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->orderBy('nama'))
                    ->form(fn(AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('kuota')->numeric()->required()->default(10),
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DetachAction::make(),
            ]);
    }
}
