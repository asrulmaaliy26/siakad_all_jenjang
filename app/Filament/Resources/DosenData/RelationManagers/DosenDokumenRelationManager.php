<?php

namespace App\Filament\Resources\DosenData\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Illuminate\Support\Facades\Storage;

class DosenDokumenRelationManager extends RelationManager
{
    protected static string $relationship = 'dokumen';
    protected static ?string $title = 'Dokumen Dosen';

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('judul_dokumen')
                    ->label('Judul Dokumen')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('tipe_dokumen')
                    ->label('Tipe')
                    ->options([
                        'materi' => 'Materi',
                        'tugas' => 'Tugas',
                        'rpp' => 'RPP',
                        'silabus' => 'Silabus',
                        'lainnya' => 'Lainnya',
                    ])
                    ->required()
                    ->default('lainnya'),

                Forms\Components\FileUpload::make('file_path')
                    ->label('Upload File')
                    ->required()
                    ->disk('public')
                    ->visibility('public')
                    ->directory(fn(\Filament\Resources\RelationManagers\RelationManager $livewire) => \App\Helpers\UploadPathHelper::uploadDosenPath($livewire->getOwnerRecord()))
                    ->storeFileNamesIn('file_name')
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull()
                    // Hapus file saat klik ❌
                    ->afterStateUpdated(function ($state, $record) {
                        if (blank($state) && $record?->foto_profil) {
                            Storage::disk('public')->delete($record->foto_profil);
                        }
                    })

                    // Hapus file lama saat upload baru
                    ->deleteUploadedFileUsing(function ($file, $record) {
                        if ($record?->foto_profil) {
                            Storage::disk('public')->delete($record->foto_profil);
                        }
                        return true;
                    }), // full width,,

                Forms\Components\Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_public')
                    ->label('Publik (Dapat dilihat mahasiswa)')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('judul_dokumen')
            ->columns([
                Tables\Columns\TextColumn::make('judul_dokumen')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipe_dokumen')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'materi' => 'success',
                        'tugas' => 'warning',
                        'rpp' => 'info',
                        'silabus' => 'primary',
                        'lainnya' => 'gray',
                    }),

                Tables\Columns\TextColumn::make('file_name')
                    ->label('Nama File')
                    ->limit(30)
                    ->icon('heroicon-m-document'),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Publik')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Optional: Fill file_size and file_type manually if needed
                        // Storage::size() etc.
                        return $data;
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
