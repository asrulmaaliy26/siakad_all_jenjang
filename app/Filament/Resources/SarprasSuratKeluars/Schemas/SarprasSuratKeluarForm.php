<?php

namespace App\Filament\Resources\SarprasSuratKeluars\Schemas;

use Filament\Schemas\Schema;

class SarprasSuratKeluarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('sarpras_surat_kategori_id')
                    ->relationship('kategori', 'nama')
                    ->required()
                    ->live(),
                \Filament\Forms\Components\Select::make('tahun_akademik_id')
                    ->relationship('tahunAkademik', 'nama')
                    ->default(\App\Models\TahunAkademik::where('status', 'Y')->first()?->id)
                    ->required(),
                \Filament\Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Pemohon')
                    ->searchable()
                    ->preload(),
                \Filament\Forms\Components\TextInput::make('nomor_surat')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Akan digenerate otomatis jika dikosongkan (setelah save)'),
                \Filament\Forms\Components\TextInput::make('perihal')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('tujuan')
                    ->required(),
                \Filament\Forms\Components\DatePicker::make('tanggal_surat')
                    ->required()
                    ->default(now()),
                \Filament\Forms\Components\RichEditor::make('isi_surat')
                    ->columnSpanFull(),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Sent' => 'Sent',
                        'Archived' => 'Archived',
                    ])
                    ->default('Draft')
                    ->required(),
            ]);
    }
}
