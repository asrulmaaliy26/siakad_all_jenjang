<?php

namespace App\Filament\Resources\SarprasBarangs\Schemas;

use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class SarprasBarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('kode_barang')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('nama_barang')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('merek')
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                \Filament\Forms\Components\Select::make('sarpras_kategori_id')
                    ->relationship('kategori', 'nama_kategori')
                    ->required()
                    ->searchable()
                    ->preload(),
                \Filament\Forms\Components\Select::make('id_jurusan')
                    ->relationship('jurusan', 'nama')
                    ->searchable()
                    ->preload(),
                \Filament\Forms\Components\Select::make('kondisi')
                    ->options([
                        'Baik' => 'Baik',
                        'Rusak Ringan' => 'Rusak Ringan',
                        'Rusak Berat' => 'Rusak Berat',
                    ])
                    ->required(),
                \Filament\Forms\Components\Select::make('status_penggunaan')
                    ->options([
                        'Tersedia' => 'Tersedia',
                        'Digunakan' => 'Digunakan',
                        'Dipinjam' => 'Dipinjam',
                        'Dihapus' => 'Dihapus',
                    ])
                    ->required(),
                \Filament\Forms\Components\DatePicker::make('tanggal_pengadaan'),
                \Filament\Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                \Filament\Forms\Components\FileUpload::make('lampiran')
                    ->multiple()
                    ->directory(function (Get $get) {
                        $tahun = $get('tanggal_pengadaan') ? date('Y', strtotime($get('tanggal_pengadaan'))) : date('Y');
                        
                        $namaKategori = 'Umum';
                        if ($get('sarpras_kategori_id')) {
                            $kategori = \App\Models\SarprasKategori::find($get('sarpras_kategori_id'));
                            if ($kategori) $namaKategori = $kategori->nama_kategori;
                        }
                        
                        $namaBarang = $get('nama_barang') ?: 'Barang';
                        
                        $cleanKategori = \Illuminate\Support\Str::slug($namaKategori);
                        $cleanBarang = \Illuminate\Support\Str::slug($namaBarang);
                        
                        return "sarpras/{$tahun}/{$cleanKategori}/{$cleanBarang}";
                    })
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->columnSpanFull(),
            ]);
    }
}
