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
                    ->sortable(),
                TextColumn::make('akademikKrs.riwayatPendidikan.siswaData.nomor_induk')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),
                TextInputColumn::make('nilai'),
                TextInputColumn::make('Nilai_UTS')
                    ->label('Nilai UTS')
                    ->type('number')
                    ->step(0.01)
                    ->sortable(),
                TextInputColumn::make('Nilai_TGS')
                    ->label('Nilai TGS')
                    ->type('number')
                    ->step(0.01)
                    ->sortable(),
                TextInputColumn::make('Nilai_UAS')
                    ->label('Nilai UAS')
                    ->type('number')
                    ->step(0.01)
                    ->sortable(),
                TextInputColumn::make('Nilai_Performance')
                    ->label('Nilai Performance')
                    ->type('number')
                    ->step(0.01)
                    ->sortable(),
                TextInputColumn::make('Nilai_Akhir')
                    ->label('Nilai Akhir')
                    ->type('number')
                    ->step(0.01)
                    ->sortable(),
                TextInputColumn::make('Nilai_Huruf')
                    ->label('Nilai Huruf')
                    ->sortable(),
                SelectColumn::make('Status_Nilai')
                    ->label('Status Nilai')
                    ->options([
                        'L' => 'Lulus',
                        'TL' => 'Tidak Lulus',
                    ])
                    ->selectablePlaceholder(false)
                    // ->extraAttributes(function ($state) {
                    //     $bg = match ($state) {
                    //         'L' => 'bg-success-100 text-success-800',
                    //         'TL' => 'bg-danger-100 text-danger-800',
                    //         default => 'bg-gray-100 text-gray-800',
                    //     };

                    //     return [
                    //         'class' => "$bg px-3 py-1.5 rounded-lg font-medium text-center inline-block w-full",
                    //     ];
                    // })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('sync_students')
                    ->label('Sync Mahasiswa')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->action(function () {
                        $record = $this->getOwnerRecord();
                        // Get all students enrolled in this class via KRS
                        $krsList = AkademikKRS::where('id_kelas', $record->id_kelas)->get();

                        foreach ($krsList as $krs) {
                            SiswaDataLJK::firstOrCreate([
                                'id_mata_pelajaran_kelas' => $record->id,
                                'id_akademik_krs' => $krs->id,
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
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
