<?php

namespace App\Filament\Resources\SiswaData\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SiswaDataPendaftarRelationManager extends RelationManager
{
    protected static string $relationship = 'pendaftar';
    protected static ?string $title = 'Data Pendaftar';

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Data Pendaftaran')
                    ->tabs([
                        Tabs\Tab::make('Data Dasar')
                            ->schema([
                                Forms\Components\TextInput::make('Nama_Lengkap')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('No_Pendaftaran')
                                    ->maxLength(255)
                                    ->disabled(fn() => auth()->user()->hasRole('murid') && !auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                                Forms\Components\Select::make('ro_program_sekolah')
                                    ->options(\App\Models\RefOption\ProgramSekolah::pluck('nilai', 'id'))
                                    ->label('Program Sekolah')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn() => auth()->user()->hasRole('murid') && !auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                                Forms\Components\Select::make('id_jurusan')
                                    ->relationship('jurusan', 'nama')
                                    ->label('Jurusan')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn() => auth()->user()->hasRole('murid') && !auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                                Forms\Components\TextInput::make('Tahun_Masuk')
                                    ->numeric()
                                    ->default(date('Y'))
                                    ->disabled(fn() => auth()->user()->hasRole('murid') && !auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                                Forms\Components\DatePicker::make('Tgl_Daftar')
                                    ->default(now())
                                    ->disabled(fn() => auth()->user()->hasRole('murid') && !auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                                Forms\Components\TextInput::make('Kelas_Program_Kuliah')
                                    ->disabled(fn() => auth()->user()->hasRole('murid') && !auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                                Forms\Components\TextInput::make('Prodi_Pilihan_1')
                                    ->disabled(fn() => auth()->user()->hasRole('murid') && !auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                                Forms\Components\TextInput::make('Prodi_Pilihan_2')
                                    ->disabled(fn() => auth()->user()->hasRole('murid') && !auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                                Forms\Components\Select::make('Jalur_PMB')
                                    ->relationship('jalurPmbRef', 'nilai')
                                    ->label('Jalur PMB'),
                                Forms\Components\FileUpload::make('Bukti_Jalur_PMB')
                                    ->directory('pendaftaran/bukti_jalur'),
                                Forms\Components\TextInput::make('Jenis_Pembiayaan'),
                                Forms\Components\FileUpload::make('Bukti_Jenis_Pembiayaan')
                                    ->directory('pendaftaran/bukti_pembiayaan'),
                                Forms\Components\Select::make('Status_Pendaftaran')
                                    ->options([
                                        'Y' => 'Ya',
                                        'N' => 'Tidak',
                                        'B' => 'Belum',
                                    ])
                                    ->disabled(fn() => auth()->user()->hasRole('murid') && !auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                            ])->columns(2),

                        Tabs\Tab::make('Data Mutasi')
                            ->schema([
                                Forms\Components\TextInput::make('NIMKO_Asal'),
                                Forms\Components\TextInput::make('Prodi_Asal'),
                                Forms\Components\TextInput::make('PT_Asal'),
                                Forms\Components\TextInput::make('Jml_SKS_Asal')->numeric(),
                                Forms\Components\TextInput::make('IPK_Asal')->numeric(),
                                Forms\Components\TextInput::make('Semester_Asal')->numeric(),
                                Forms\Components\FileUpload::make('Pengantar_Mutasi')
                                    ->directory('pendaftaran/mutasi'),
                                Forms\Components\FileUpload::make('Transkip_Asal')
                                    ->directory('pendaftaran/mutasi'),
                            ])->columns(2),

                        Tabs\Tab::make('Dokumen & Foto')
                            ->schema([
                                Forms\Components\FileUpload::make('Legalisir_Ijazah')->directory('pendaftaran/dokumen'),
                                Forms\Components\FileUpload::make('Legalisir_SKHU')->directory('pendaftaran/dokumen'),
                                Forms\Components\FileUpload::make('Copy_KTP')->directory('pendaftaran/dokumen'),
                                Forms\Components\FileUpload::make('Foto_BW_3x3')->directory('pendaftaran/foto'),
                                Forms\Components\FileUpload::make('Foto_BW_3x4')->directory('pendaftaran/foto'),
                                Forms\Components\FileUpload::make('Foto_Warna_5x6')->directory('pendaftaran/foto'),
                                Forms\Components\FileUpload::make('File_Foto_Berwarna')->directory('pendaftaran/foto'),
                            ])->columns(2),

                        Tabs\Tab::make('Tes Tulis')
                            ->schema([
                                Forms\Components\DatePicker::make('Tgl_Tes_Tulis'),
                                Forms\Components\TextInput::make('N_Agama')->numeric(),
                                Forms\Components\TextInput::make('N_Umum')->numeric(),
                                Forms\Components\TextInput::make('N_Psiko')->numeric(),
                                Forms\Components\TextInput::make('N_Jumlah_Tes_Tulis')->numeric(),
                                Forms\Components\TextInput::make('N_Rerata_Tes_Tulis')->numeric(),
                            ])->columns(3),

                        Tabs\Tab::make('Tes Lisan')
                            ->schema([
                                Forms\Components\DatePicker::make('Tgl_Tes_Lisan'),
                                Forms\Components\TextInput::make('N_Potensi_Akademik')->numeric(),
                                Forms\Components\TextInput::make('N_Baca_al_Quran')->numeric(),
                                Forms\Components\TextInput::make('N_Baca_Kitab_Kuning')->numeric(),
                                Forms\Components\TextInput::make('N_Jumlah_Tes_Lisan')->numeric(),
                                Forms\Components\TextInput::make('N_Rearata_Tes_Lisan')->numeric(),
                            ])->columns(3),

                        Tabs\Tab::make('Kelulusan')
                            ->schema([
                                Forms\Components\Select::make('Status_Kelulusan')
                                    ->options([
                                        'L' => 'Lulus',
                                        'TL' => 'Tidak Lulus',
                                        'B' => 'Belum',
                                    ])
                                    ->disabled(fn() => auth()->user()->hasRole('murid') && !auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                                Forms\Components\TextInput::make('Jumlah_Nilai')->numeric(),
                                Forms\Components\TextInput::make('Rata_Rata')->numeric(),
                                Forms\Components\TextInput::make('Rekomendasi_1'),
                                Forms\Components\TextInput::make('Rekomendasi_2'),
                                Forms\Components\TextInput::make('No_SK_Kelulusan'),
                                Forms\Components\DatePicker::make('Tgl_SK_Kelulusan'),
                                Forms\Components\TextInput::make('Diterima_di_Prodi'),
                            ])->columns(2),

                        Tabs\Tab::make('Pembayaran')
                            ->schema([
                                Forms\Components\TextInput::make('Biaya_Pendaftaran')->numeric()->prefix('Rp')->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0),
                                Forms\Components\FileUpload::make('Bukti_Biaya_Daftar')->directory('pendaftaran/pembayaran'),
                                Forms\Components\Toggle::make('status_valid')->label('Valid')
                                    ->disabled(fn() => auth()->user()->hasRole('murid') && !auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                                Forms\Components\TextInput::make('verifikator'),
                                Forms\Components\TextInput::make('reff'),
                            ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Nama_Lengkap')
            ->columns([
                Tables\Columns\TextColumn::make('Nama_Lengkap'),
                Tables\Columns\TextColumn::make('No_Pendaftaran'),
                Tables\Columns\TextColumn::make('jurusan.nama')
                    ->label('Jurusan'),
                Tables\Columns\TextColumn::make('programSekolahRef.nilai')
                    ->label('Program Sekolah'),
                Tables\Columns\TextColumn::make('Status_Pendaftaran'),
                Tables\Columns\TextColumn::make('Status_Kelulusan'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
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
