<?php

namespace App\Filament\Resources\AkademikKrs\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Models\MataPelajaranKelas;
use Illuminate\Database\Eloquent\Builder;

class SiswaDataLjkRelationManager extends RelationManager
{
    protected static string $relationship = 'siswaDataLjk';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_mata_pelajaran_kelas')
                    ->label('Mata Pelajaran Kelas')
                    ->relationship('mataPelajaranKelas', 'id', modifyQueryUsing: function (Builder $query) {
                        $query->with(['mataPelajaranKurikulum.mataPelajaranMaster', 'dosenData', 'ruangKelas', 'kelas.programKelas']);

                        $user = auth()->user();
                        if ($user && $user->isPengajar()) {
                            $query->whereHas('dosenData', function ($q) use ($user) {
                                $q->where('user_id', $user->id);
                            });
                        }
                    })
                    ->getOptionLabelFromRecordUsing(function (MataPelajaranKelas $record) {
                        $namaMatkul = $record->mataPelajaranKurikulum->mataPelajaranMaster->nama ?? '-';
                        $hari = $record->hari ?? '-';
                        $jam = $record->jam ?? '-';
                        $dosen = $record->dosenData->nama ?? 'Belum ada Dosen';
                        $ruang = $record->ruangKelas->nama ?? '-';
                        $program = $record->kelas->programKelas->nilai ?? '-';
                        $semester = $record->kelas->semester ?? '-';
                        return "Smt {$semester} | {$program} | {$namaMatkul} - {$hari}, {$jam} ({$ruang}) - {$dosen}";
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nilai')
            ->columns([
                TextColumn::make('mataPelajaranKelas.mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mataPelajaranKelas.kelas.programKelas.nilai')
                    ->label('Program')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mataPelajaranKelas.dosenData.nama')
                    ->label('Dosen Pengajar')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jadwal')
                    ->label('Jadwal')
                    ->state(function ($record) {
                        $mpk = $record->mataPelajaranKelas;
                        return $mpk ? "{$mpk->hari}, {$mpk->jam}" : '-';
                    })
                    ->sortable(['hari', 'jam']),
                TextColumn::make('mataPelajaranKelas.ruangKelas.nama')
                    ->label('Ruang')
                    ->placeholder('-'),
                TextColumn::make('Nilai_Huruf')
                    ->label('Nilai')
                    ->placeholder('-'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\Action::make('tambah')
                    ->label('Tambah Mata Pelajaran')
                    ->modalHeading('Pilih Mata Pelajaran')
                    ->modalContent(fn() => view('filament.resources.akademik-krs.actions.view-subjects', ['record' => $this->getOwnerRecord(), 'excludeTaken' => true]))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->closeModalByClickingAway(false)
                    ->modalWidth('7xl')
                    ->disabled(function () {
                        $krs  = $this->getOwnerRecord();

                        /** @var \App\Models\User|null $user */
                        $user = \Illuminate\Support\Facades\Auth::user();

                        // Cek role user
                        $isMurid = $user && $user->isMurid();

                        // Untuk role murid: tombol disabled hanya jika syarat_krs = 'Y' (terkunci)
                        if ($isMurid) {
                            return ($krs->syarat_aktif ?? 'N') === 'Y';
                        }

                        // Untuk role selain murid (admin/guru): tombol selalu enabled
                        return false;
                    })
                    ->tooltip(function () {
                        $krs = $this->getOwnerRecord();

                        /** @var \App\Models\User|null $user */
                        $user = \Illuminate\Support\Facades\Auth::user();

                        // Tooltip hanya untuk murid jika KRS terkunci
                        if ($user && $user->isMurid() && ($krs->syarat_krs ?? 'N') === 'Y') {
                            return 'KRS sudah dikunci – syarat KRS telah terpenuhi';
                        }

                        return null;
                    })
                    ->before(function ($record, \Filament\Actions\Action $action) {
                        /** @var \App\Models\User|null $user */
                        $user = \Illuminate\Support\Facades\Auth::user();

                        // Validasi pembayaran hanya untuk role murid
                        if ($user?->isMurid() && ($record->status_bayar ?? 'N') === 'N') {
                            \Filament\Notifications\Notification::make()
                                ->title('Pembayaran Belum Selesai')
                                ->body('Anda belum melunasi pembayaran. Silakan selesaikan pembayaran terlebih dahulu untuk mengakses fitur ini.')
                                ->warning()
                                ->persistent()
                                ->send();
                            $action->halt();
                        }
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->disabled(function () {
                        $krs = $this->getOwnerRecord();
                        return auth()->user()?->isMurid()
                            || ($krs->syarat_krs ?? 'N') === 'Y';
                    }),
                DeleteAction::make()
                    ->disabled(function () {
                        $krs = $this->getOwnerRecord();
                        return auth()->user()?->isMurid()
                            || ($krs->syarat_krs ?? 'N') === 'Y';
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && $user->isPengajar()) {
            $query->whereHas('mataPelajaranKelas', function ($q) use ($user) {
                $q->whereHas('dosenData', function ($dq) use ($user) {
                    $dq->where('user_id', $user->id);
                });
            });
        }

        return $query;
    }
}
