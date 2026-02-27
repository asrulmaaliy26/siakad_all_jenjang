<?php

namespace App\Filament\Resources\PengajuanSurats;

use App\Filament\Resources\PengajuanSurats\Pages\ManagePengajuanSurats;
use App\Models\PengajuanSurat;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class PengajuanSuratResource extends Resource
{
    protected static ?string $model = PengajuanSurat::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Persuratan Mahasiswa';
    protected static string|UnitEnum|null $navigationGroup = 'Layanan Mahasiswa';
    protected static ?int $navigationSort = 50;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        /** @var \App\Models\User $user */
        $user = \Filament\Facades\Filament::auth()->user();

        return $schema
            ->components([
                Section::make('Informasi Mahasiswa & Surat')
                    ->schema([
                        Select::make('id_riwayat_pendidikan')
                            ->label('Mahasiswa')
                            ->relationship('riwayatPendidikan', 'id', function ($query) use ($user) {
                                $query->with('siswa');
                                if ($user && $user->isMurid()) {
                                    $query->whereHas('siswa', function ($q) use ($user) {
                                        $q->where('user_id', $user->id);
                                    });
                                }
                            })
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->siswa?->nama_lengkap} ({$record->nomor_induk})")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn($operation) => $user && $user->isMurid() && $operation === 'create'),

                        Select::make('id_tahun_akademik')
                            ->label('Tahun Akademik')
                            ->relationship('tahunAkademik', 'nama')
                            ->required()
                            ->default(fn() => \App\Models\TahunAkademik::where('status', 'Y')->first()?->id),

                        Select::make('jenis_surat')
                            ->label('Jenis Surat')
                            ->options(PengajuanSurat::getJenisOptions())
                            ->required()
                            ->live(),

                        Textarea::make('keperluan')
                            ->label('Alasan / Keperluan / Keterangan')
                            ->required()
                            ->rows(4),
                    ])
                    ->columnSpan(2),

                Section::make('Status & Dokumen')
                    ->schema([
                        Select::make('status')
                            ->label('Status Request')
                            ->options([
                                'diajukan' => 'Diajukan',
                                'diproses' => 'Sedang Diproses',
                                'disetujui' => 'Disetujui / Selesai',
                                'ditolak' => 'Ditolak',
                            ])
                            ->default('diajukan')
                            ->required()
                            ->disabled(fn() => $user && $user->isMurid()),

                        Textarea::make('catatan_admin')
                            ->label('Catatan Admin')
                            ->disabled(fn() => $user && $user->isMurid()),

                        FileUpload::make('file_pendukung')
                            ->label('File Pendukung (Mahasiswa)')
                            ->directory('persuratan/pendukung'),

                        FileUpload::make('file_hasil')
                            ->label('File Hasil (Admin)')
                            ->directory('persuratan/hasil')
                            ->openable()
                            ->downloadable()
                            ->disabled(fn() => $user && $user->isMurid()),
                    ])
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('riwayatPendidikan.siswa.nama_lengkap')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jenis_surat')
                    ->label('Jenis Surat')
                    ->formatStateUsing(fn(string $state): string => PengajuanSurat::getJenisOptions()[$state] ?? $state)
                    ->badge()
                    ->color('info'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'diajukan' => 'warning',
                        'diproses' => 'info',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Tgl Pengajuan')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('jenis_surat')
                    ->options(PengajuanSurat::getJenisOptions()),
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'diajukan' => 'Diajukan',
                        'diproses' => 'Sedang Diproses',
                        'disetujui' => 'Disetujui / Selesai',
                        'ditolak' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                Action::make('cetak')
                    ->label('Cetak Pengajuan')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('cetak.pengajuan.surat', $record->id))
                    ->openUrlInNewTab(),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User $user */
        $user  = \Filament\Facades\Filament::auth()->user();

        if ($user && $user->isMurid()) {
            $query->whereHas('riwayatPendidikan.siswaData', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePengajuanSurats::route('/'),
        ];
    }
}
