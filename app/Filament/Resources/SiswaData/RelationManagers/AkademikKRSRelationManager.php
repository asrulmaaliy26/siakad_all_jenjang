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
use Filament\Actions\DeleteBulkAction;

class AkademikKRSRelationManager extends RelationManager
{
    protected static string $relationship = 'akademikKrs';
    protected static ?string $title = 'Akademik KRS';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('id_riwayat_pendidikan')
                ->label('Riwayat Pendidikan')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('id_kelas')
                ->label('Kelas')
                ->numeric(),

            // Data KRS
            Forms\Components\TextInput::make('semester')
                ->label('Semester')
                ->required(),

            // Forms\Components\TextInput::make('tahun_akademik')
            //     ->label('Tahun Akademik')
            //     ->required(),

            Forms\Components\TextInput::make('jumlah_sks')
                ->label('Jumlah SKS')
                ->numeric(),

            Forms\Components\DatePicker::make('tgl_krs')
                ->label('Tanggal KRS'),

            Forms\Components\TextInput::make('kode_ta')
                ->label('Kode TA'),

            Forms\Components\TextInput::make('kwitansi_krs')
                ->label('Kwitansi KRS'),

            // ENUM fields
            Forms\Components\Select::make('status_bayar')
                ->label('Status Bayar')
                ->options([
                    'Y' => 'Ya',
                    'N' => 'Tidak',
                ])
                ->default('N'),

            Forms\Components\Select::make('syarat_uts')
                ->label('Syarat UTS')
                ->options([
                    'Y' => 'Ya',
                    'N' => 'Tidak',
                ])
                ->default('N'),

            Forms\Components\Select::make('syarat_krs')
                ->label('Syarat KRS')
                ->options([
                    'Y' => 'Ya',
                    'N' => 'Tidak',
                ])
                ->default('N'),

            Forms\Components\Select::make('status_aktif')
                ->label('Status Aktif')
                ->options([
                    'Y' => 'Aktif',
                    'N' => 'Tidak Aktif',
                ])
                ->default('Y'),

            // Timestamps
            Forms\Components\DatePicker::make('created_at')
                ->label('Dibuat')
                ->disabled(),

            Forms\Components\DatePicker::make('updated_at')
                ->label('Diperbarui')
                ->disabled(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // Relasi / Foreign Key
                Tables\Columns\TextColumn::make('riwayatPendidikan.siswa.nama')
                    ->label('Mahasiswa'), // Asumsi relasi bernama riwayatPendidikan

                Tables\Columns\TextColumn::make('kelas.programKelas.nilai')
                    ->label('Kelas'), // Asumsi relasi bernama kelas

                // Data KRS
                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester'),

                // Tables\Columns\TextColumn::make('tahun_akademik')
                //     ->label('Tahun Akademik'),

                Tables\Columns\TextColumn::make('jumlah_sks')
                    ->label('SKS'),

                // Tables\Columns\TextColumn::make('kode_ta')
                //     ->label('Kode TA'),

                Tables\Columns\BadgeColumn::make('status_bayar')
                    ->label('Status Bayar')
                    ->formatStateUsing(fn($state) => $state === 'Y' ? 'Lunas' : 'Belum Lunas')
                    ->colors([
                        'success' => fn($state) => $state === 'Y',
                        'danger' => fn($state) => $state === 'N',
                    ]),

                Tables\Columns\BadgeColumn::make('syarat_uts')
                    ->label('Syarat UTS')
                    ->formatStateUsing(fn($state) => $state === 'Y' ? 'Terpenuhi' : 'Belum')
                    ->colors([
                        'success' => fn($state) => $state === 'Y',
                        'danger' => fn($state) => $state === 'N',
                    ]),

                Tables\Columns\BadgeColumn::make('syarat_krs')
                    ->label('Syarat KRS')
                    ->formatStateUsing(fn($state) => $state === 'Y' ? 'Terpenuhi' : 'Belum')
                    ->colors([
                        'success' => fn($state) => $state === 'Y',
                        'danger' => fn($state) => $state === 'N',
                    ]),

                Tables\Columns\BadgeColumn::make('status_aktif')
                    ->label('Status Aktif')
                    ->formatStateUsing(fn($state) => $state === 'Y' ? 'Aktif' : 'Tidak Aktif')
                    ->colors([
                        'success' => fn($state) => $state === 'Y',
                        'danger' => fn($state) => $state === 'N',
                    ]),

            ])
            ->filters([
                // Bisa tambahkan filter semester, tahun akademik, atau status_bayar
                Tables\Filters\SelectFilter::make('semester'),
                Tables\Filters\SelectFilter::make('tahun_akademik'),
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
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
    public function isReadOnly(): bool
    {
        return false;
    }
}
