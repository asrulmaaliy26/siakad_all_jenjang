<?php

namespace App\Filament\Resources\SiswaData;

use App\Filament\Resources\SiswaData\Pages\CreateSiswaData;
use App\Filament\Resources\SiswaData\Pages\EditSiswaData;
use App\Filament\Resources\SiswaData\Pages\ListSiswaData;
use App\Filament\Resources\SiswaData\Pages\ViewSiswaData;
use App\Filament\Resources\SiswaData\Schemas\SiswaDataForm;
use App\Filament\Resources\SiswaData\Tables\SiswaDataTable;
use App\Models\SiswaData;
use App\Filament\Resources\SiswaData\RelationManagers\RiwayatPendidikanRelationManager;
use App\Filament\Resources\SiswaData\RelationManagers\AkademikKRSRelationManager;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Fieldset;
use UnitEnum;

class SiswaDataResource extends Resource
{
    protected static ?string $model = SiswaData::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    // protected static string | UnitEnum | null $navigationGroup = 'Master Data Siswa';
    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    // protected static ?string $navigationLabel = 'Siswa/Mahasiswa';

    public static function getNavigationLabel(): string
    {
        return \App\Helpers\SiakadTerm::pesertaDidik() . '';
    }

    public static function getModelLabel(): string
    {
        return \App\Helpers\SiakadTerm::pesertaDidik();
    }

    protected static ?int $navigationSort = 16;

    public static function form(Schema $schema): Schema
    {
        return SiswaDataForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiswaDataTable::configure($table);
    }

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->columns(3)
            ->schema([
                    Group::make([
                        Section::make()->schema([
                            ImageEntry::make('foto_profil')
                                ->hiddenLabel()
                                ->circular()
                                ->size(150),
                            TextEntry::make('nama_lengkap')
                                ->hiddenLabel()
                                ->size('lg')
                                ->weight('bold'),
                            TextEntry::make('status_siswa')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'aktif' => 'success',
                                    'tidak aktif' => 'danger',
                                    default => 'warning',
                                }),
                        ])
                    ])->columnSpan(['default' => 3, 'md' => 1]),

                    Group::make([
                        Section::make('BIODATA DIRI')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextEntry::make('nama_lengkap')->label('Nama'),
                                    TextEntry::make('tempat_tanggal_lahir')
                                        ->label('Tempat, Tgl Lahir')
                                        ->state(fn ($record) => ($record->kota_lahir ?? '-') . ', ' . ($record->tanggal_lahir ? \Carbon\Carbon::parse($record->tanggal_lahir)->format('d-m-Y') : '-')),
                                    TextEntry::make('nisn')->label('NISN')->default('-'),
                                    TextEntry::make('nik')->label('NIK')->default('-'),
                                    TextEntry::make('jenis_kelamin')->label('Gender')->default('-'),
                                    TextEntry::make('agama')
                                        ->label('Agama')
                                        ->formatStateUsing(fn ($state) => \App\Models\SiswaData::$pddikti_agama[$state] ?? $state)
                                        ->default('-'),
                                    TextEntry::make('kewarganegaraan')->label('Kewarganegaraan')->default('-'),
                                    TextEntry::make('alamat')->label('Alamat')->columnSpanFull()->default('-'),
                                    TextEntry::make('email_account')->label('Email')->default('-'),
                                    TextEntry::make('no_telepon_wa')->label('No. Whatsapp')->default('-'),
                                    TextEntry::make('anak_ke')
                                        ->label('Anak Ke')
                                        ->state(fn ($record) => ($record->anak_ke ?? '-') . ' Dari ' . ($record->jumlah_saudara ?? '-') . ' bersaudara'),
                                    TextEntry::make('status_kawin')->label('Status Perkawinan')->default('-'),
                                    TextEntry::make('pekerjaan')->label('Pekerjaan')->default('-'),
                                    TextEntry::make('biaya_ditanggung')->label('Biaya Ditanggung?')->default('-'),
                                    TextEntry::make('jenis_domisili')->label('Jenis Domisili')->default('-'),
                                    TextEntry::make('status_asal_sekolah')->label('Status Asal Sekolah')->default('-'),
                                    TextEntry::make('jenis_slta')->label('Jenis Sekolah Asal')->default('-'),
                                    TextEntry::make('kejuruan_slta')->label('Jurusan')->default('-'),
                                    TextEntry::make('asal_slta')->label('Nama Asal Sekolah')->default('-'),
                                    TextEntry::make('alamat_lengkap_sekolah_asal')->label('Alamat Sekolah')->columnSpanFull()->default('-'),
                                    TextEntry::make('tahun_lulus_slta')->label('Tahun Lulus')->default('-'),
                                    TextEntry::make('nomor_seri_ijazah_slta')->label('No. Seri Ijazah')->default('-'),
                                ]),
                            ]),

                        Section::make('Dosen Wali')
                            ->schema([
                                TextEntry::make('riwayatPendidikanAktif.dosenWali.nama_lengkap')
                                    ->label('Nama')
                                    ->default('-'),
                            ]),

                        Section::make('BIODATA ORANG TUA')
                            ->schema([
                                Grid::make(2)->schema([
                                    Fieldset::make('Profil Ayah')->schema([
                                        TextEntry::make('orangTua.Nama_Ayah')->label('Nama')->default('-'),
                                        TextEntry::make('orangTua.Nomor_KTP_Ayah')->label('NIK')->default('-'),
                                        TextEntry::make('orangTua.tempat_tanggal_lahir_ayah')
                                            ->label('Tempat, Tgl. lahir')
                                            ->state(fn ($record) => ($record->orangTua?->Tempat_Lhr_Ayah ?? '-') . ', ' . ($record->orangTua?->Tgl_Lhr_Ayah ? \Carbon\Carbon::parse($record->orangTua->Tgl_Lhr_Ayah)->format('d-m-Y') : '-')),
                                        TextEntry::make('orangTua.Agama_Ayah')
                                            ->label('Agama')
                                            ->formatStateUsing(fn ($state) => \App\Models\SiswaData::$pddikti_agama[$state] ?? $state)
                                            ->default('-'),
                                        TextEntry::make('orangTua.Gol_Darah_Ayah')->label('Gol. Darah')->default('-'),
                                        TextEntry::make('orangTua.Kewarganegaraan_Ayah')->label('Kewarganegaraan')->default('-'),
                                        TextEntry::make('orangTua.Alamat_Ayah')->label('Alamat')->default('-'),
                                        TextEntry::make('orangTua.Pendidikan_Terakhir_Ayah')
                                            ->label('Pendidikan')
                                            ->formatStateUsing(fn ($state) => \App\Models\SiswaDataOrangTua::$pddikti_pendidikan[$state] ?? $state)
                                            ->default('-'),
                                        TextEntry::make('orangTua.Penghasilan_Ayah')
                                            ->label('Penghasilan')
                                            ->formatStateUsing(fn ($state) => \App\Models\SiswaDataOrangTua::$pddikti_penghasilan[$state] ?? $state)
                                            ->default('-'),
                                        TextEntry::make('orangTua.Pekerjaan_Ayah')
                                            ->label('Pekerjaan')
                                            ->formatStateUsing(fn ($state) => \App\Models\SiswaDataOrangTua::$pddikti_pekerjaan[$state] ?? $state)
                                            ->default('-'),
                                    ])->columns(1),

                                    Fieldset::make('Profil Ibu')->schema([
                                        TextEntry::make('orangTua.Nama_Ibu')->label('Nama')->default('-'),
                                        TextEntry::make('orangTua.Nomor_KTP_Ibu')->label('NIK')->default('-'),
                                        TextEntry::make('orangTua.tempat_tanggal_lahir_ibu')
                                            ->label('Tempat, Tgl. lahir')
                                            ->state(fn ($record) => ($record->orangTua?->Tempat_Lhr_Ibu ?? '-') . ', ' . ($record->orangTua?->Tgl_Lhr_Ibu ? \Carbon\Carbon::parse($record->orangTua->Tgl_Lhr_Ibu)->format('d-m-Y') : '-')),
                                        TextEntry::make('orangTua.Agama_Ibu')
                                            ->label('Agama')
                                            ->formatStateUsing(fn ($state) => \App\Models\SiswaData::$pddikti_agama[$state] ?? $state)
                                            ->default('-'),
                                        TextEntry::make('orangTua.Gol_Darah_Ibu')->label('Gol. Darah')->default('-'),
                                        TextEntry::make('orangTua.Kewarganegaraan_Ibu')->label('Kewarganegaraan')->default('-'),
                                        TextEntry::make('orangTua.Alamat_Ibu')->label('Alamat')->default('-'),
                                        TextEntry::make('orangTua.Pendidikan_Terakhir_Ibu')
                                            ->label('Pendidikan')
                                            ->formatStateUsing(fn ($state) => \App\Models\SiswaDataOrangTua::$pddikti_pendidikan[$state] ?? $state)
                                            ->default('-'),
                                        TextEntry::make('orangTua.Penghasilan_Ibu')
                                            ->label('Penghasilan')
                                            ->formatStateUsing(fn ($state) => \App\Models\SiswaDataOrangTua::$pddikti_penghasilan[$state] ?? $state)
                                            ->default('-'),
                                        TextEntry::make('orangTua.Pekerjaan_Ibu')
                                            ->label('Pekerjaan')
                                            ->formatStateUsing(fn ($state) => \App\Models\SiswaDataOrangTua::$pddikti_pekerjaan[$state] ?? $state)
                                            ->default('-'),
                                    ])->columns(1),
                                ]),
                            ]),
                    ])->columnSpan(['default' => 3, 'md' => 2]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            RiwayatPendidikanRelationManager::class,
            AkademikKRSRelationManager::class,
            RelationManagers\SiswaDataPendaftarRelationManager::class,
            RelationManagers\SiswaDataOrangTuaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiswaData::route('/'),
            'create' => CreateSiswaData::route('/create'),
            'download-files' => \App\Filament\Resources\SiswaData\SiswaDataResource\Pages\DownloadPublicFiles::route('/download-files'),
            'view' => ViewSiswaData::route('/{record}'),
            'edit' => EditSiswaData::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()
            ->where(function ($query) {
                // Tampilkan siswa yang:
                // 1. Tidak memiliki data pendaftar (siswa lama/input manual)
                // 2. ATAU memiliki data pendaftar dengan Status_Pendaftaran = 'Y' 
                $query->doesntHave('pendaftar')
                    ->orWhereHas('pendaftar', function ($q) {
                        $q->where('Status_Pendaftaran', 'Y');
                    });
            });

        $user = auth()->user();
        if ($user && $user->isMurid()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }
}
