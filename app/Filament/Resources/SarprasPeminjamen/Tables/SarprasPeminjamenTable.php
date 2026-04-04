<?php

namespace App\Filament\Resources\SarprasPeminjamen\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class SarprasPeminjamenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('barang.nama_barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jumlah_pinjam')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tanggal_pinjam')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Diajukan' => 'warning',
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        'Dipinjam' => 'info',
                        'Dikembalikan' => 'gray',
                        'Telat' => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Diajukan' => 'Diajukan',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                        'Dipinjam' => 'Dipinjam',
                        'Dikembalikan' => 'Dikembalikan',
                        'Telat' => 'Telat',
                    ]),
            ])
            ->actions([
                // Admin Actions
                Action::make('setujui')
                    ->label('Setujui')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn($record) => auth()->user()->isAdmin() && $record->status === 'Diajukan')
                    ->action(function ($record) {
                        // Create Surat Keluar
                        $kategori = \App\Models\SarprasSuratKategori::where('kode', 'PINJAM')->first();
                        $tahunAkademikId = \App\Models\TahunAkademik::where('status', 'Y')->first()?->id;

                        $surat = \App\Models\SarprasSuratKeluar::create([
                            'sarpras_surat_kategori_id' => $kategori?->id,
                            'tahun_akademik_id' => $tahunAkademikId,
                            'user_id' => $record->user_id,
                            'perihal' => 'Peminjaman Barang: ' . $record->barang->nama_barang,
                            'tujuan' => $record->user->name,
                            'tanggal_surat' => now(),
                            'isi_surat' => "<p>Dengan ini menerangkan bahwa:</p><ul><li><strong>Nama Barang:</strong> {$record->barang->nama_barang}</li><li><strong>Jumlah:</strong> {$record->jumlah_pinjam} unit</li><li><strong>Tanggal Pinjam:</strong> {$record->tanggal_pinjam->format('d-m-Y')}</li><li><strong>Estimasi Kembali:</strong> {$record->estimasi_kembali->format('d-m-Y')}</li></ul>",
                            'status' => 'Sent',
                        ]);

                        $record->update([
                            'status' => 'Disetujui',
                            'sarpras_surat_keluar_id' => $surat->id,
                        ]);
                    }),
                Action::make('tolak')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn($record) => auth()->user()->isAdmin() && $record->status === 'Diajukan')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update(['status' => 'Ditolak'])),

                // User/Admin Actions
                Action::make('cetak_surat')
                    ->label('Cetak Surat')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->visible(fn($record) => $record->canBePrinted())
                    ->url(fn($record) => route('sarpras.surat-keluar.cetak', $record->sarpras_surat_keluar_id))
                    ->openUrlInNewTab(),

                Action::make('ambil_barang')
                    ->label('Ambil Barang')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('primary')
                    ->visible(fn($record) => auth()->user()->isAdmin() && $record->status === 'Disetujui')
                    ->action(fn($record) => $record->update(['status' => 'Dipinjam'])),

                Action::make('kembalikan')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-backspace')
                    ->color('warning')
                    ->visible(fn($record) => $record->canBeReturned())
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update([
                        'status' => 'Dikembalikan',
                        'tanggal_kembali' => now(),
                    ])),

                EditAction::make()
                    ->visible(fn($record) => auth()->user()->isAdmin()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
