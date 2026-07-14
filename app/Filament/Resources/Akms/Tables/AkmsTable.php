<?php

namespace App\Filament\Resources\Akms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class AkmsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                \Filament\Tables\Columns\TextColumn::make('riwayatPendidikan.siswaData.nama')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                \Filament\Tables\Columns\TextColumn::make('riwayatPendidikan.nomor_induk')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('riwayatPendidikan.jurusan.nama')
                    ->label('Jurusan')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('riwayatPendidikan.tahunAkademik.nama')
                    ->label('Angkatan')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('semester')
                    ->label('Semester')
                    ->getStateUsing(fn($record) => $record->riwayatPendidikan?->getSemester($record->tgl_krs ?? $record->created_at))
                    ->badge()
                    ->color('info'),
                \Filament\Tables\Columns\TextColumn::make('status_aktif')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn($record) => $record->status_aktif == 'Y' ? 'Aktif' : 'Tidak Aktif')
                    ->color(fn($state) => $state === 'Aktif' ? 'success' : 'danger'),
                \Filament\Tables\Columns\TextColumn::make('sks_diambil')
                    ->label('SKS SMT')
                    ->badge()
                    ->color('warning'),
                \Filament\Tables\Columns\TextColumn::make('sks_total')
                    ->label('SKS Total')
                    ->badge()
                    ->color('primary'),
                \Filament\Tables\Columns\TextColumn::make('ips')
                    ->label('IPS')
                    ->weight('bold'),
                \Filament\Tables\Columns\TextColumn::make('ipk')
                    ->label('IPK')
                    ->weight('bold'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label('Detail AKM'),
            ])
            ->toolbarActions([
                //
            ])
            ->modifyQueryUsing(function (\Illuminate\Database\Eloquent\Builder $query) {
                // If the user is a student, only show their own AKM
                $user = auth()->user();
                if ($user && $user->isMurid()) {
                    $query->whereHas('riwayatPendidikan.siswaData', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                }
            });
    }
}
