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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
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
                                TextInput::make('Nama_Lengkap')
                                    ->maxLength(255),
                                TextInput::make('No_Pendaftaran')
                                    ->maxLength(255)
                                    ->disabled(fn() => auth()->user()->isMurid()),
                                Select::make('ro_program_sekolah')
                                    ->options(\App\Models\RefOption\ProgramSekolah::pluck('nilai', 'id'))
                                    ->label('Program Sekolah')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn() => auth()->user()->isMurid()),
                                Select::make('id_jurusan')
                                    ->relationship('jurusan', 'nama')
                                    ->label('Jurusan')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn() => auth()->user()->isMurid()),
                                Select::make('id_tahun_akademik')
                                    ->label('Tahun Akademik')
                                    ->relationship('tahunAkademik', 'nama')
                                    ->default(fn() => \App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id)
                                    ->required()
                                    ->disabled(fn() => auth()->user()->isMurid()),
                                Forms\Components\DatePicker::make('Tgl_Daftar')
                                    ->default(now())
                                    ->disabled(fn() => auth()->user()->isMurid()),
                                TextInput::make('Kelas_Program_Kuliah')
                                    ->disabled(fn() => auth()->user()->isMurid()),
                                TextInput::make('Prodi_Pilihan_1')
                                    ->disabled(fn() => auth()->user()->isMurid()),
                                TextInput::make('Prodi_Pilihan_2')
                                    ->disabled(fn() => auth()->user()->isMurid()),
                                Forms\Components\Select::make('Jalur_PMB')
                                    ->relationship('jalurPmbRef', 'nilai')
                                    ->label('Jalur PMB'),
                                Forms\Components\FileUpload::make('Bukti_Jalur_PMB')
                                    ->directory('pendaftaran/bukti_jalur'),
                                TextInput::make('Jenis_Pembiayaan'),
                                Forms\Components\FileUpload::make('Bukti_Jenis_Pembiayaan')
                                    ->directory('pendaftaran/bukti_pembiayaan'),
                                Forms\Components\Select::make('Status_Pendaftaran')
                                    ->options([
                                        'Y' => 'Ya',
                                        'N' => 'Tidak',
                                        'B' => 'Belum',
                                    ])
                                    ->disabled(fn() => auth()->user()->isMurid()),
                            ])->columns(2),

                        Tabs\Tab::make('Data Mutasi')
                            ->schema([
                                TextInput::make('NIMKO_Asal'),
                                TextInput::make('Prodi_Asal'),
                                TextInput::make('PT_Asal'),
                                TextInput::make('Jml_SKS_Asal')->numeric(),
                                TextInput::make('IPK_Asal')->numeric(),
                                TextInput::make('Semester_Asal')->numeric(),
                                Forms\Components\FileUpload::make('Pengantar_Mutasi')
                                    ->directory('pendaftaran/mutasi'),
                                Forms\Components\FileUpload::make('Transkip_Asal')
                                    ->directory('pendaftaran/mutasi'),
                            ])->columns(2),

                        Tabs\Tab::make('Dokumen & Foto')
                            ->schema([
                                Forms\Components\FileUpload::make('Legalisir_Ijazah')
                                    ->disk('public')
                                    ->multiple()
                                    ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'Legalisir_Ijazah'))
                                    ->visibility('public')
                                    ->preserveFilenames()
                                    ->maxSize(10240)
                                    ->downloadable()
                                    ->openable(),

                                Forms\Components\FileUpload::make('Legalisir_SKHU')
                                    ->disk('public')
                                    ->multiple()
                                    ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'Legalisir_SKHU'))
                                    ->visibility('public')
                                    ->preserveFilenames()
                                    ->maxSize(10240)
                                    ->downloadable()
                                    ->openable(),

                                Forms\Components\FileUpload::make('Copy_KTP')
                                    ->disk('public')
                                    ->multiple()
                                    ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'Copy_KTP'))
                                    ->visibility('public')
                                    ->preserveFilenames()
                                    ->maxSize(10240)
                                    ->downloadable()
                                    ->openable(),

                                Forms\Components\FileUpload::make('Foto_BW_3x3')
                                    ->image()
                                    ->disk('public')
                                    ->multiple()
                                    ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'Foto_BW_3x3'))
                                    ->visibility('public')
                                    ->preserveFilenames()
                                    ->maxSize(10240)
                                    ->downloadable()
                                    ->openable(),

                                Forms\Components\FileUpload::make('Foto_BW_3x4')
                                    ->image()
                                    ->disk('public')
                                    ->multiple()
                                    ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'Foto_BW_3x4'))
                                    ->visibility('public')
                                    ->preserveFilenames()
                                    ->maxSize(10240)
                                    ->downloadable()
                                    ->openable(),

                                Forms\Components\FileUpload::make('Foto_Warna_5x6')
                                    ->image()
                                    ->disk('public')
                                    ->multiple()
                                    ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'Foto_Warna_5x6'))
                                    ->visibility('public')
                                    ->preserveFilenames()
                                    ->maxSize(10240)
                                    ->downloadable()
                                    ->openable(),

                                Forms\Components\FileUpload::make('File_Foto_Berwarna')
                                    ->image()
                                    ->disk('public')
                                    ->multiple()
                                    ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadPendaftarPath($get, $record, 'File_Foto_Berwarna'))
                                    ->visibility('public')
                                    ->preserveFilenames()
                                    ->maxSize(10240)
                                    ->downloadable()
                                    ->openable(),
                            ])->columns(2),

                        Tabs\Tab::make('Tes Tulis')
                            ->schema([
                                Forms\Components\DatePicker::make('Tgl_Tes_Tulis'),
                                TextInput::make('N_Agama')->numeric(),
                                TextInput::make('N_Umum')->numeric(),
                                TextInput::make('N_Psiko')->numeric(),
                                TextInput::make('N_Jumlah_Tes_Tulis')->numeric(),
                                TextInput::make('N_Rerata_Tes_Tulis')->numeric(),
                            ])->columns(3),

                        Tabs\Tab::make('Tes Lisan')
                            ->schema([
                                Forms\Components\DatePicker::make('Tgl_Tes_Lisan'),
                                TextInput::make('N_Potensi_Akademik')->numeric(),
                                TextInput::make('N_Baca_al_Quran')->numeric(),
                                TextInput::make('N_Baca_Kitab_Kuning')->numeric(),
                                TextInput::make('N_Jumlah_Tes_Lisan')->numeric(),
                                TextInput::make('N_Rearata_Tes_Lisan')->numeric(),
                            ])->columns(3),

                        Tabs\Tab::make('Kelulusan')
                            ->schema([
                                Forms\Components\Select::make('Status_Kelulusan')
                                    ->options([
                                        'L' => 'Lulus',
                                        'TL' => 'Tidak Lulus',
                                        'B' => 'Belum',
                                    ])
                                    ->disabled(fn() => auth()->user()->isMurid()),
                                TextInput::make('Jumlah_Nilai')->numeric(),
                                TextInput::make('Rata_Rata')->numeric(),
                                TextInput::make('Rekomendasi_1'),
                                TextInput::make('Rekomendasi_2'),
                                TextInput::make('No_SK_Kelulusan'),
                                Forms\Components\DatePicker::make('Tgl_SK_Kelulusan'),
                                TextInput::make('Diterima_di_Prodi'),
                            ])->columns(2),

                        Tabs\Tab::make('Pembayaran')
                            ->schema([
                                TextInput::make('Biaya_Pendaftaran')->numeric()->prefix('Rp'),
                                Forms\Components\FileUpload::make('Bukti_Biaya_Daftar')->directory('pendaftaran/pembayaran'),
                                Forms\Components\Toggle::make('status_valid')->label('Valid')
                                    ->disabled(function () {
                                        /** @var \App\Models\User|null $user */
                                        $user = \Illuminate\Support\Facades\Auth::user();
                                        return $user?->isMurid();
                                    }),
                                TextInput::make('verifikator'),
                                TextInput::make('reff'),
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
