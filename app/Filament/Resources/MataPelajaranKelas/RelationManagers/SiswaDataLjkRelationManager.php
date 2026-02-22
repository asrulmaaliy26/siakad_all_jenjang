<?php

namespace App\Filament\Resources\MataPelajaranKelas\RelationManagers;

use App\Filament\Resources\MataPelajaranKelas\MataPelajaranKelasResource;
use App\Models\AkademikKRS;
use App\Models\SiswaDataLJK;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\SelectColumn;

class SiswaDataLjkRelationManager extends RelationManager
{
    protected static string $relationship = 'siswaDataLjk';

    protected static ?string $title = 'Data LJK / Nilai';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('akademikKrs.riwayatPendidikan.siswaData.nama')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable()
                    ->hidden(fn() => auth()->user()?->isMurid()),
                TextColumn::make('akademikKrs.riwayatPendidikan.siswaData.nomor_induk')
                    ->label('NIM')
                    ->searchable()
                    ->sortable()
                    ->hidden(fn() => auth()->user()?->isMurid()),
                TextInputColumn::make('nilai')
                    ->disabled(fn() => auth()->user()?->isMurid()),
                TextInputColumn::make('Nilai_UTS')
                    ->label('Nilai UTS')
                    ->type('number')
                    ->step(0.01)
                    ->sortable()
                    ->disabled(fn() => auth()->user()?->isMurid()),
                TextInputColumn::make('Nilai_TGS_1')
                    ->label('Nilai TGS 1')
                    ->type('number')
                    ->step(0.01)
                    ->sortable()
                    ->disabled(fn() => auth()->user()?->isMurid()),
                TextInputColumn::make('Nilai_TGS_2')
                    ->label('Nilai TGS 2')
                    ->type('number')
                    ->step(0.01)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->disabled(fn() => auth()->user()?->isMurid()),
                TextInputColumn::make('Nilai_TGS_3')
                    ->label('Nilai TGS 3')
                    ->type('number')
                    ->step(0.01)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->disabled(fn() => auth()->user()?->isMurid()),
                TextInputColumn::make('Nilai_UAS')
                    ->label('Nilai UAS')
                    ->type('number')
                    ->step(0.01)
                    ->sortable()
                    ->disabled(fn() => auth()->user()?->isMurid()),
                TextInputColumn::make('Nilai_Performance')
                    ->label('Nilai Performance')
                    ->type('number')
                    ->step(0.01)
                    ->sortable()
                    ->disabled(fn() => auth()->user()?->isMurid()),
                TextInputColumn::make('Nilai_Akhir')
                    ->label('Nilai Akhir')
                    ->type('number')
                    ->step(0.01)
                    ->sortable()
                    ->disabled(fn() => auth()->user()?->isMurid()),
                TextInputColumn::make('Nilai_Huruf')
                    ->label('Nilai Huruf')
                    ->sortable()
                    ->disabled(fn() => auth()->user()?->isMurid()),
                TextColumn::make('ljk_tugas_1')
                    ->label('File Tugas 1')
                    ->formatStateUsing(fn($state) => $state ? 'Lihat File' : '-')
                    ->url(fn($record) => $record->ljk_tugas_1 ? asset('storage/' . $record->ljk_tugas_1) : null)
                    ->openUrlInNewTab()
                    ->color(fn($state) => $state ? 'primary' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ctt_tugas_1')
                    ->label('Catatan Tugas 1')
                    ->html()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ljk_tugas_2')
                    ->label('File Tugas 2')
                    ->formatStateUsing(fn($state) => $state ? 'Lihat File' : '-')
                    ->url(fn($record) => $record->ljk_tugas_2 ? asset('storage/' . $record->ljk_tugas_2) : null)
                    ->openUrlInNewTab()
                    ->color(fn($state) => $state ? 'primary' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ctt_tugas_2')
                    ->label('Catatan Tugas 2')
                    ->html()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ljk_tugas_3')
                    ->label('File Tugas 3')
                    ->formatStateUsing(fn($state) => $state ? 'Lihat File' : '-')
                    ->url(fn($record) => $record->ljk_tugas_3 ? asset('storage/' . $record->ljk_tugas_3) : null)
                    ->openUrlInNewTab()
                    ->color(fn($state) => $state ? 'primary' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ctt_tugas_3')
                    ->label('Catatan Tugas 3')
                    ->html()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                SelectColumn::make('Status_Nilai')
                    ->label('Status Nilai')
                    ->options([
                        'L' => 'Lulus',
                        'TL' => 'Tidak Lulus',
                    ])
                    ->selectablePlaceholder(false)
                    ->sortable()
                    ->disabled(fn() => auth()->user() && auth()->user()->isMurid()),
            ])
            ->filters([
                //
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();

                if ($user && $user->isMurid()) {
                    // Murid hanya melihat data LJK/nilai miliknya sendiri
                    $query->whereHas('akademikKrs.riwayatPendidikan.siswaData', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                }
            })
            ->headerActions([
                Action::make('sync_students')
                    ->label('Sync Mahasiswa')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn() => ! auth()->user()?->isMurid())
                    ->action(function () {
                        $record = $this->getOwnerRecord();
                        $krsList = AkademikKRS::where('id_kelas', $record->id_kelas)->get();

                        foreach ($krsList as $krs) {
                            SiswaDataLJK::firstOrCreate([
                                'id_mata_pelajaran_kelas' => $record->id,
                                'id_akademik_krs'         => $krs->id,
                            ], [
                                'nilai' => 0,
                            ]);
                        }

                        Notification::make()
                            ->title('Data berhasil disinkronisasi')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                DeleteAction::make()
                    ->visible(fn() => ! auth()->user()?->isMurid()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn() => ! auth()->user()?->isMurid()),
                ]),
            ]);
    }
}
