<?php

namespace App\Filament\Resources\PklLembagas\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PklLembagaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Lembaga')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Lembaga')
                            ->required()
                            ->columnSpanFull(),
                        
                        TextInput::make('kontak')
                            ->label('Kontak/Telepon'),
                        
                        TextInput::make('website')
                            ->label('Website')
                            ->url(),
                        
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Profil & Kerjasama')
                    ->schema([
                        RichEditor::make('profil')
                            ->label('Profil Lembaga'),
                        
                        FileUpload::make('file_kerjasama')
                            ->label('File Kerjasama (PDF)')
                            ->disk('public')
                            ->directory(function ($get) {
                                $nama = $get('nama') ?? 'default';
                                return "lembaga_pkl/{$nama}";
                            })
                            ->acceptedFileTypes(['application/pdf'])
                            ->openable()
                            ->downloadable(),
                    ]),
            ]);
    }
}
