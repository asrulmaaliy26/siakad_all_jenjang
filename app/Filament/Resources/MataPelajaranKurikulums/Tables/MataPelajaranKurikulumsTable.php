<?php

namespace App\Filament\Resources\MataPelajaranKurikulums\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MataPelajaranKurikulumsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                TextColumn::make('id'),
                TextColumn::make('kurikulum.nama')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('mataPelajaranMaster.nama')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('semester')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('id_tahun_akademik')->default(fn () => session('global_tahun_akademik_id') ?? \App\Models\TahunAkademik::where('status', 'Y')->latest()->first()?->id)
                    ->label('Tahun Akademik')
                    ->options(\App\Models\TahunAkademik::orderByDesc('id')->get()->pluck('nama', 'id')->toArray())
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data) {
                        if (empty($data['value'])) return;
                        $query->whereHas('kurikulum', function ($q) use ($data) {
                            $q->where('id_tahun_akademik', $data['value']);
                        });
                    })
                    
                    ->searchable()
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('import')
                    ->label('Import Distribusi MP')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('file')
                            ->label('File Excel')
                            ->storeFiles(false)
                            ->acceptedFileTypes([
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $file = is_array($data['file']) ? $data['file'][0] : $data['file'];
                        $filePath = $file->getRealPath();
                        $import = new \App\Imports\MataPelajaranKurikulumImport();
                        \Maatwebsite\Excel\Facades\Excel::import($import, $filePath);

                        \Filament\Notifications\Notification::make()
                            ->title('Import Selesai')
                            ->body($import->successCount . ' baris berhasil diimpor.')
                            ->success()
                            ->send();
                    }),
                \pxlrbt\FilamentExcel\Actions\Tables\ExportAction::make()
            ]);
    }
}
