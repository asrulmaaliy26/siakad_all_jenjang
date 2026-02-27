<?php

namespace App\Filament\Resources\PengaturanPendaftarans;

use App\Filament\Resources\PengaturanPendaftarans\Pages\ManagePengaturanPendaftarans;
use App\Models\PengaturanPendaftaran;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Support\Facades\Storage;

class PengaturanPendaftaranResource extends Resource
{
    protected static ?string $model = PengaturanPendaftaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected static string | UnitEnum | null $navigationGroup = 'Pendaftaran';
    protected static ?string $navigationLabel = 'Pengaturan Pendaftaran';
    protected static ?string $modelLabel = 'Pengaturan Pendaftaran';
    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'tahun_akademik';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Pengaturan')
                    ->tabs([
                        // Tab 1: Biaya
                        Tabs\Tab::make('Biaya Pendaftaran')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make('Pengaturan Biaya')
                                    ->description('Atur biaya pendaftaran berdasarkan jalur PMB')
                                    ->schema([
                                        TextInput::make('biaya_reguler')
                                            ->label('Biaya Jalur Reguler')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(100000)
                                            ->required()
                                            ->helperText('Biaya untuk jalur reguler, prestasi, dan pindahan'),

                                        TextInput::make('biaya_beasiswa')
                                            ->label('Biaya Jalur Beasiswa')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(50000)
                                            ->required()
                                            ->helperText('Biaya untuk jalur beasiswa (KIP, dll)'),
                                    ])
                                    ->columns(2),
                            ]),

                        // Tab 2: Visual
                        Tabs\Tab::make('Tampilan')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Foto & Banner')
                                    ->description('Upload foto untuk ditampilkan di halaman pendaftaran')
                                    ->schema([
                                        FileUpload::make('foto_header')
                                            ->label('Foto Header')
                                            ->image()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('pendaftaran/header')
                                            ->maxSize(2048)
                                            ->helperText('Foto header untuk halaman pendaftaran (max 2MB)')
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

                                        FileUpload::make('foto_banner')
                                            ->label('Foto Banner')
                                            ->image()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('pendaftaran/banner')
                                            ->maxSize(2048)
                                            ->helperText('Foto banner untuk halaman pendaftaran (max 2MB)')
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

                                        Textarea::make('deskripsi_pendaftaran')
                                            ->label('Deskripsi Pendaftaran')
                                            ->rows(4)
                                            ->helperText('Deskripsi yang ditampilkan di halaman pendaftaran'),

                                        FileUpload::make('brosur_pendaftaran')
                                            ->label('Brosur Pendaftaran')
                                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('pendaftaran/brosur')
                                            ->maxSize(5120)
                                            ->helperText('Upload file brosur (PDF/Gambar) maksimal 5MB'),
                                    ])
                                    ->columns(1),
                            ]),

                        // Tab 3: Akses
                        Tabs\Tab::make('Periode & Gelombang')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                Section::make('Pengaturan Umum Pendaftaran')
                                    ->description('Status utama pendaftaran')
                                    ->schema([
                                        Toggle::make('status_pendaftaran')
                                            ->label('Buka Pendaftaran (Utama)')
                                            ->helperText('Matikan ini untuk menutup paksa seluruh pendaftaran tanpa menghiraukan waktu gelombang.')
                                            ->default(true),
                                    ]),

                                Section::make('Gelombang 1')
                                    ->schema([
                                        Toggle::make('gelombang_1_aktif')
                                            ->label('Aktifkan Gelombang 1'),
                                        \Filament\Forms\Components\DatePicker::make('gelombang_1_buka')
                                            ->label('Tanggal Buka Gelombang 1'),
                                        \Filament\Forms\Components\DatePicker::make('gelombang_1_tutup')
                                            ->label('Tanggal Tutup Gelombang 1'),
                                    ])->columns(3),

                                Section::make('Gelombang 2')
                                    ->schema([
                                        Toggle::make('gelombang_2_aktif')
                                            ->label('Aktifkan Gelombang 2'),
                                        \Filament\Forms\Components\DatePicker::make('gelombang_2_buka')
                                            ->label('Tanggal Buka Gelombang 2'),
                                        \Filament\Forms\Components\DatePicker::make('gelombang_2_tutup')
                                            ->label('Tanggal Tutup Gelombang 2'),
                                    ])->columns(3),

                                Section::make('Gelombang 3')
                                    ->schema([
                                        Toggle::make('gelombang_3_aktif')
                                            ->label('Aktifkan Gelombang 3'),
                                        \Filament\Forms\Components\DatePicker::make('gelombang_3_buka')
                                            ->label('Tanggal Buka Gelombang 3'),
                                        \Filament\Forms\Components\DatePicker::make('gelombang_3_tutup')
                                            ->label('Tanggal Tutup Gelombang 3'),
                                    ])->columns(3),
                            ]),

                        // Tab 4: Info Tambahan
                        Tabs\Tab::make('Informasi')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Informasi Umum')
                                    ->schema([
                                        Select::make('id_tahun_akademik')
                                            ->label('Tahun Akademik')
                                            ->relationship('tahunAkademik', 'nama')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->helperText('Pilih tahun akademik untuk periode pendaftaran ini')
                                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nama} - {$record->periode}"),

                                        Textarea::make('pengumuman')
                                            ->label('Pengumuman')
                                            ->rows(3)
                                            ->helperText('Pengumuman yang ditampilkan di halaman pendaftaran'),

                                        TextInput::make('kontak_admin')
                                            ->label('Kontak Admin')
                                            ->tel()
                                            ->placeholder('08123456789'),

                                        TextInput::make('email_admin')
                                            ->label('Email Admin')
                                            ->email()
                                            ->placeholder('admin@example.com'),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tahun_akademik')
            ->columns([
                TextColumn::make('tahunAkademik.nama')
                    ->label('Tahun Akademik')
                    ->formatStateUsing(fn($record) => $record->tahunAkademik ? "{$record->tahunAkademik->nama} - {$record->tahunAkademik->periode}" : '-')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                ToggleColumn::make('status_pendaftaran')
                    ->label('Status Pendaftaran')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle')
                    ->onColor('success')
                    ->offColor('danger')
                    ->sortable(),

                TextColumn::make('biaya_reguler')
                    ->label('Biaya Reguler')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('biaya_beasiswa')
                    ->label('Biaya Beasiswa')
                    ->money('IDR')
                    ->sortable(),

                ImageColumn::make('foto_header')
                    ->label('Foto Header')
                    ->circular()
                    ->toggleable(),

                TextColumn::make('gelombang_aktif')
                    ->label('Gelombang Saat Ini')
                    ->getStateUsing(fn($record) => $record->getGelombangAktif())
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\Action::make('download_brosur')
                    ->label('Brosur')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn($record) => $record->brosur_pendaftaran ? Storage::url($record->brosur_pendaftaran) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn($record) => !empty($record->brosur_pendaftaran)),
                \Filament\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false); // Biasanya hanya ada 1 record
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePengaturanPendaftarans::route('/'),
        ];
    }
}
