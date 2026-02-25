<?php

namespace App\Filament\Resources\SiswaData\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;

class AkademikKRSRelationManager extends RelationManager
{
    protected static string $relationship = 'akademikKrs';
    protected static ?string $title = 'Akademik KRS';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('id_riwayat_pendidikan')
                ->label('Riwayat Pendidikan')
                ->relationship('riwayatPendidikan', 'nomor_induk')
                ->getOptionLabelFromRecordUsing(fn($record) => "{$record->siswa->nama} - {$record->nomor_induk}")
                ->searchable()
                ->preload()
                ->disabled(fn() => auth()->user()?->isMurid()),

            Forms\Components\TextInput::make('jumlah_sks')
                ->label('Jumlah SKS')
                ->numeric()
                ->default(24)
                ->disabled(fn() => auth()->user()?->isMurid()),

            Forms\Components\DatePicker::make('tgl_krs')
                ->label('Tanggal KRS')
                ->default(now())
                ->disabled(fn() => auth()->user()?->isMurid()),

            Forms\Components\Select::make('kode_tahun')
                ->label('Tahun Akademik')
                ->options(\App\Models\TahunAkademik::all()->mapWithKeys(fn($item) => [$item->nama => "{$item->nama} - {$item->periode}"]))
                ->default(\App\Models\TahunAkademik::where('status', 'Aktif')->first()?->nama)
                ->searchable()
                ->required()
                ->disabled(fn() => auth()->user()?->isMurid()),

            // ENUM fields
            Forms\Components\Select::make('status_bayar')
                ->label('Status Bayar')
                ->options([
                    'Y' => 'Lunas',
                    'N' => 'Belum Lunas',
                ])
                ->default('N')
                ->disabled(fn() => auth()->user()?->isMurid()),

            Forms\Components\Select::make('syarat_krs')
                ->label('Syarat KRS')
                ->options([
                    'Y' => 'Terpenuhi',
                    'N' => 'Belum',
                ])
                ->default('N')
                ->disabled(fn() => auth()->user()?->isMurid()),

            Forms\Components\Select::make('status_aktif')
                ->label('Status Aktif')
                ->options([
                    'Y' => 'Aktif',
                    'N' => 'Tidak Aktif',
                ])
                ->default('Y')
                ->disabled(fn() => auth()->user()?->isMurid()),

            Forms\Components\FileUpload::make('kwitansi_krs')
                ->label('Kwitansi')
                ->multiple()
                ->disk('public')
                ->directory('krs/kwitansi'),

            Forms\Components\FileUpload::make('berkas_lain')
                ->label('Berkas Lain')
                ->multiple()
                ->disk('public')
                ->directory('krs/berkas'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('semester')
                    ->label('Smt')
                    ->badge()
                    ->color('info')
                    ->getStateUsing(function ($record) {
                        return $record->riwayatPendidikan?->getSemester($record->tgl_krs ?? $record->created_at);
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('tahunAkademik.nama')
                    ->label('Tahun Akademik')
                    ->formatStateUsing(fn($record) => $record->tahunAkademik ? "{$record->tahunAkademik->nama} - {$record->tahunAkademik->periode}" : $record->kode_tahun)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kelas.programKelas.nilai')
                    ->label('Kelas')
                    ->listWithLineBreaks()
                    ->bulleted(),

                Tables\Columns\TextColumn::make('jumlah_sks')
                    ->label('SKS')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status_bayar')
                    ->label('Bayar')
                    ->formatStateUsing(fn($state) => $state === 'Y' ? 'Lunas' : 'Belum')
                    ->colors([
                        'success' => 'Y',
                        'danger' => 'N',
                    ]),

                Tables\Columns\BadgeColumn::make('syarat_krs')
                    ->label('KRS')
                    ->formatStateUsing(fn($state) => $state === 'Y' ? 'OK' : 'Belum')
                    ->colors([
                        'success' => 'Y',
                        'danger' => 'N',
                    ]),

                Tables\Columns\BadgeColumn::make('status_aktif')
                    ->label('Aktif')
                    ->formatStateUsing(fn($state) => $state === 'Y' ? 'Y' : 'N')
                    ->colors([
                        'success' => 'Y',
                        'danger' => 'N',
                    ]),

                Tables\Columns\TextColumn::make('kwitansi_krs')
                    ->label('Kwitansi')
                    ->getStateUsing(fn($record) => count($record->kwitansi_krs ?? []) > 0 ? count($record->kwitansi_krs) . ' File' : '-')
                    ->badge()
                    ->color(fn($state) => $state !== '-' ? 'success' : 'gray')
                    ->action(
                        Action::make('view_kwitansi')
                            ->modalHeading('Lihat Kwitansi')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                            ->fillForm(fn($record) => [
                                'kwitansi_krs' => $record->kwitansi_krs,
                            ])
                            ->form([
                                Forms\Components\FileUpload::make('kwitansi_krs')
                                    ->label('Berkas Kwitansi')
                                    ->multiple()
                                    ->disk('public')
                                    ->disabled()
                                    ->openable()
                                    ->downloadable()
                                    ->dehydrated(false)
                            ])
                    ),

                Tables\Columns\TextColumn::make('berkas_lain')
                    ->label('Berkas')
                    ->getStateUsing(fn($record) => count($record->berkas_lain ?? []) > 0 ? count($record->berkas_lain) . ' File' : '-')
                    ->badge()
                    ->color(fn($state) => $state !== '-' ? 'success' : 'gray')
                    ->action(
                        Action::make('view_berkas_lain')
                            ->modalHeading('Lihat Berkas Pendukung')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                            ->fillForm(fn($record) => [
                                'berkas_lain' => $record->berkas_lain,
                            ])
                            ->form([
                                Forms\Components\FileUpload::make('berkas_lain')
                                    ->label('Berkas Pendukung')
                                    ->multiple()
                                    ->disk('public')
                                    ->disabled()
                                    ->openable()
                                    ->downloadable()
                                    ->dehydrated(false)
                            ])
                    ),
            ])
            ->filters([
                // Bisa tambahkan filter semester, tahun akademik, atau status_bayar
                Tables\Filters\SelectFilter::make('semester'),
                Tables\Filters\SelectFilter::make('kode_tahun')
                    ->label('Tahun Akademik')
                    ->options(fn() => \App\Models\TahunAkademik::all()->mapWithKeys(fn($item) => [$item->nama => "{$item->nama} - {$item->periode}"])->toArray())
                    ->default(\App\Models\TahunAkademik::where('status', 'Aktif')->first()?->nama)
                    ->searchable(),
                Tables\Filters\SelectFilter::make('status_bayar')
                    ->options([
                        'Y' => 'Lunas',
                        'N' => 'Belum Lunas',
                    ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                // dd(EditAction::class),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => ! auth()->user()?->isMurid()),
                ]),
            ]);
    }
    public function isReadOnly(): bool
    {
        return false;
    }
}
