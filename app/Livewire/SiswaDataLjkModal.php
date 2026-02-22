<?php

namespace App\Livewire;

use App\Models\AkademikKrs;
use App\Models\SiswaDataLJK;
use App\Models\MataPelajaranKelas;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Notifications\Notification;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Filters\SelectFilter;

class SiswaDataLjkModal extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;

    public int $recordId;
    public array $takenSubjectIds = [];
    public ?int $studentJurusanId = null;
    public bool $excludeTaken = false;
    public bool $isKrsLocked = false;
    public bool $isBayarLunas = true;

    public function mount(int $recordId, bool $excludeTaken = false)
    {
        $this->recordId = $recordId;
        $this->excludeTaken = $excludeTaken;
        $this->refreshTakenSubjects();

        $krs = AkademikKrs::with('riwayatPendidikan')->find($recordId);
        if ($krs && $krs->riwayatPendidikan) {
            $this->studentJurusanId = $krs->riwayatPendidikan->id_jurusan;
        }

        // Kunci jika syarat_krs = 'Y'
        $this->isKrsLocked = ($krs?->syarat_krs ?? 'N') === 'Y';

        // Status pembayaran
        $this->isBayarLunas = ($krs?->status_bayar ?? 'N') === 'Y';
    }

    public function refreshTakenSubjects()
    {
        $this->takenSubjectIds = SiswaDataLJK::where('id_akademik_krs', $this->recordId)
            ->pluck('id_mata_pelajaran_kelas')
            ->toArray();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MataPelajaranKelas::query()
                    ->with(['mataPelajaranKurikulum.mataPelajaranMaster', 'dosenData', 'ruangKelas'])
            )
            ->modifyQueryUsing(function (Builder $query) {
                if ($this->excludeTaken && !empty($this->takenSubjectIds)) {
                    $query->whereNotIn('id', $this->takenSubjectIds);
                } elseif (!empty($this->takenSubjectIds)) {
                    $idsString = implode(',', $this->takenSubjectIds);
                    // Order by whether the ID is in the filtered list (putting them first)
                    // 1 = True (In List), 0 = False. DESC sorts 1 first.
                    $query->orderByRaw("FIELD(id, {$idsString}) DESC");
                }
            })
            ->groups([
                Group::make('status_ambil')
                    ->label('Status Pengambilan')
                    ->getKeyFromRecordUsing(
                        fn(MataPelajaranKelas $record) =>
                        in_array($record->id, $this->takenSubjectIds) ? 'taken' : 'available'
                    )
                    ->getTitleFromRecordUsing(
                        fn(MataPelajaranKelas $record) =>
                        in_array($record->id, $this->takenSubjectIds) ? 'Sudah Diambil' : 'Belum Diambil'
                    )
                    ->orderQueryUsing(function (Builder $query, string $direction) {
                        if (empty($this->takenSubjectIds)) {
                            return $query;
                        }
                        $idsString = implode(',', $this->takenSubjectIds);
                        $query->orderByRaw("id IN ({$idsString}) " . ($direction === 'asc' ? 'DESC' : 'ASC'));
                    })
                    ->collapsible(),
            ])
            ->defaultGroup('status_ambil')
            ->columns([
                TextColumn::make('mataPelajaranKurikulum.mataPelajaranMaster.nama')
                    ->label('Mata Kuliah')
                    ->searchable()
                    ->sortable()
                    ->description(fn(MataPelajaranKelas $record) => $record->mataPelajaranKurikulum->kode ?? '-'),

                TextColumn::make('dosenData.nama')
                    ->label('Dosen')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('hari')
                    ->label('Jadwal')
                    ->formatStateUsing(fn(MataPelajaranKelas $record) => "{$record->hari}, {$record->jam}")
                    ->sortable(),

                TextColumn::make('ruangKelas.nama')
                    ->label('Ruang')
                    ->placeholder('-'),

                TextColumn::make('status_ambil')
                    ->label('Status')
                    ->badge()
                    ->state(fn(MataPelajaranKelas $record) => in_array($record->id, $this->takenSubjectIds) ? 'Diambil' : 'Belum Diambil')
                    ->colors([
                        'success' => 'Diambil',
                        'gray' => 'Belum Diambil',
                    ]),
            ])
            ->filters([
                SelectFilter::make('status_ambil')
                    ->label('Status Pengambilan')
                    ->options([
                        'taken' => 'Sudah Diambil',
                        'available' => 'Belum Diambil',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'taken') {
                            $query->whereIn('id', $this->takenSubjectIds);
                        } elseif ($data['value'] === 'available') {
                            $query->whereNotIn('id', $this->takenSubjectIds);
                        }
                    }),
                SelectFilter::make('jurusan')
                    ->label('Jurusan / Prodi')
                    ->options(\App\Models\Jurusan::pluck('nama', 'id'))
                    ->default($this->studentJurusanId)
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('mataPelajaranKurikulum.mataPelajaranMaster', function (Builder $q) use ($data) {
                                $q->where('id_jurusan', $data['value']);
                            });
                        }
                    }),
            ])
            ->actions([
                Action::make('add')
                    ->label('Ambil')
                    ->icon('heroicon-m-plus')
                    ->button()
                    ->color('primary')
                    ->action(function (MataPelajaranKelas $record) {
                        // Cek pembayaran untuk murid
                        $user = auth()->user();
                        if ($user && $user->isMurid() && ! $this->isBayarLunas) {
                            Notification::make()
                                ->title('Pembayaran Belum Lunas')
                                ->body('Anda belum dapat mengambil mata kuliah. Harap selesaikan pembayaran KRS terlebih dahulu.')
                                ->danger()
                                ->persistent()
                                ->send();
                            return;
                        }

                        SiswaDataLJK::create([
                            'id_akademik_krs'         => $this->recordId,
                            'id_mata_pelajaran_kelas' => $record->id,
                        ]);

                        $this->refreshTakenSubjects();

                        Notification::make()
                            ->title('Mata kuliah berhasil ditambahkan')
                            ->success()
                            ->send();
                    })
                    ->visible(
                        fn(MataPelajaranKelas $record) =>
                        ! $this->isKrsLocked
                            && ! in_array($record->id, $this->takenSubjectIds)
                    ),

                Action::make('remove')
                    ->label('Batal')
                    ->icon('heroicon-m-minus')
                    ->button()
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (MataPelajaranKelas $record) {
                        SiswaDataLJK::where('id_akademik_krs', $this->recordId)
                            ->where('id_mata_pelajaran_kelas', $record->id)
                            ->delete();

                        $this->refreshTakenSubjects();

                        Notification::make()
                            ->title('Mata kuliah dibatalkan')
                            ->success()
                            ->send();
                    })
                    ->visible(
                        fn(MataPelajaranKelas $record) =>
                        ! $this->isKrsLocked
                            && in_array($record->id, $this->takenSubjectIds)
                    ),

                // Info saat KRS dikunci
                Action::make('locked_info')
                    ->label('KRS Dikunci')
                    ->icon('heroicon-m-lock-closed')
                    ->button()
                    ->color('gray')
                    ->disabled()
                    ->visible(fn() => $this->isKrsLocked),
            ])
            ->striped();
    }

    public function render()
    {
        return view('livewire.siswa-data-ljk-modal');
    }
}
