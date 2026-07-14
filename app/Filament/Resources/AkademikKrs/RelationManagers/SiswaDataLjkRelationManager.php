<?php

namespace App\Filament\Resources\AkademikKrs\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
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
                    ->relationship('mataPelajaranKelas', 'id', modifyQueryUsing: function (Builder $query, RelationManager $livewire) {
                        $query->with(['mataPelajaranKurikulum.mataPelajaranMaster', 'dosenData', 'ruangKelas', 'kelas.programKelas']);

                        $user = auth()->user();
                        if ($user && $user->isPengajar()) {
                            $query->whereHas('dosenData', function ($q) use ($user) {
                                $q->where('user_id', $user->id);
                            });
                        }

                        $krs = $livewire->getOwnerRecord();
                        if ($krs && $krs->id_tahun_akademik) {
                            $query->whereHas('kelas', function ($q) use ($krs) {
                                $q->where('id_tahun_akademik', $krs->id_tahun_akademik);
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
                \Filament\Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
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

                // Kolom pelanggaran UTS
                TextColumn::make('jml_pelanggaran_uts')
                    ->label('Plg UTS')
                    ->badge()
                    ->color(fn ($state) => $state >= 3 ? 'danger' : ($state >= 2 ? 'warning' : ($state > 0 ? 'info' : 'gray')))
                    ->formatStateUsing(fn ($state) => $state > 0 ? "{$state}x" : '-')
                    ->tooltip('Jumlah pelanggaran selama ujian UTS')
                    ->toggleable(),

                // Kolom pelanggaran UAS
                TextColumn::make('jml_pelanggaran_uas')
                    ->label('Plg UAS')
                    ->badge()
                    ->color(fn ($state) => $state >= 3 ? 'danger' : ($state >= 2 ? 'warning' : ($state > 0 ? 'info' : 'gray')))
                    ->formatStateUsing(fn ($state) => $state > 0 ? "{$state}x" : '-')
                    ->tooltip('Jumlah pelanggaran selama ujian UAS')
                    ->toggleable(),

                // Status cekal ujian UTS
                TextColumn::make('cekal_ujian_uts')
                    ->label('Cekal UTS')
                    ->badge()
                    ->color(fn ($state) => $state === 'Y' ? 'danger' : 'gray')
                    ->formatStateUsing(fn ($state) => $state === 'Y' ? '🔒 Diblokir' : 'Aktif')
                    ->toggleable(),

                // Status cekal ujian UAS
                TextColumn::make('cekal_ujian_uas')
                    ->label('Cekal UAS')
                    ->badge()
                    ->color(fn ($state) => $state === 'Y' ? 'danger' : 'gray')
                    ->formatStateUsing(fn ($state) => $state === 'Y' ? '🔒 Diblokir' : 'Aktif')
                    ->toggleable(),
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

                // Lihat log pelanggaran (hanya admin/dosen)
                Action::make('lihat_pelanggaran')
                    ->label('Log Pelanggaran')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('warning')
                    ->visible(fn () => !auth()->user()?->isMurid())
                    ->modalHeading(fn ($record) => 'Log Pelanggaran Ujian – ' . ($record->mataPelajaranKelas?->mataPelajaranKurikulum?->mataPelajaranMaster?->nama ?? '-'))
                    ->modalContent(function ($record) {
                        $logUts = $record->ctt_pelanggaran_uts;
                        $logUas = $record->ctt_pelanggaran_uas;
                        $jmlUts = $record->jml_pelanggaran_uts ?? 0;
                        $jmlUas = $record->jml_pelanggaran_uas ?? 0;
                        $cekalUts = $record->cekal_ujian_uts === 'Y';
                        $cekalUas = $record->cekal_ujian_uas === 'Y';

                        $html  = '<div style="font-family:monospace;font-size:13px;line-height:1.7;color:#e2e8f0;background:#0f172a;border-radius:10px;padding:20px;">';

                        // UTS
                        $html .= '<div style="margin-bottom:20px;">';
                        $html .= '<div style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Pelanggaran UTS</div>';
                        $html .= '<div style="margin-bottom:8px;">';
                        $html .= '<span style="background:' . ($cekalUts ? '#7f1d1d' : '#1e293b') . ';color:' . ($cekalUts ? '#fca5a5' : '#94a3b8') . ';padding:2px 10px;border-radius:12px;font-size:12px;">';
                        $html .= $cekalUts ? '🔒 DIBLOKIR' : '✅ Aktif';
                        $html .= '</span> &nbsp; <span style="color:#64748b;">' . $jmlUts . '/3 pelanggaran</span></div>';
                        if ($logUts) {
                            $lines = explode("\n", $logUts);
                            foreach ($lines as $line) {
                                $html .= '<div style="padding:4px 0;border-bottom:1px solid #1e293b;color:#fbbf24;">' . htmlspecialchars($line) . '</div>';
                            }
                        } else {
                            $html .= '<div style="color:#4b5563;font-style:italic;">Tidak ada catatan pelanggaran UTS.</div>';
                        }
                        $html .= '</div>';

                        // UAS
                        $html .= '<div>';
                        $html .= '<div style="font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Pelanggaran UAS</div>';
                        $html .= '<div style="margin-bottom:8px;">';
                        $html .= '<span style="background:' . ($cekalUas ? '#7f1d1d' : '#1e293b') . ';color:' . ($cekalUas ? '#fca5a5' : '#94a3b8') . ';padding:2px 10px;border-radius:12px;font-size:12px;">';
                        $html .= $cekalUas ? '🔒 DIBLOKIR' : '✅ Aktif';
                        $html .= '</span> &nbsp; <span style="color:#64748b;">' . $jmlUas . '/3 pelanggaran</span></div>';
                        if ($logUas) {
                            $lines = explode("\n", $logUas);
                            foreach ($lines as $line) {
                                $html .= '<div style="padding:4px 0;border-bottom:1px solid #1e293b;color:#fbbf24;">' . htmlspecialchars($line) . '</div>';
                            }
                        } else {
                            $html .= '<div style="color:#4b5563;font-style:italic;">Tidak ada catatan pelanggaran UAS.</div>';
                        }
                        $html .= '</div></div>';

                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn () => \Filament\Actions\Action::make('tutup')->label('Tutup')->close())
                    ->modalWidth('2xl'),

                // Reset cekal ujian (hanya admin/dosen)
                Action::make('reset_cekal')
                    ->label('Reset Cekal')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->visible(fn () => !auth()->user()?->isMurid())
                    ->requiresConfirmation()
                    ->modalHeading('Reset Akses Ujian Mahasiswa')
                    ->modalDescription('Ini akan mereset status cekal ujian UTS dan UAS. Jumlah pelanggaran akan direset ke 0. Log pelanggaran tetap tersimpan.')
                    ->modalSubmitActionLabel('Ya, Reset Sekarang')
                    ->action(function ($record) {
                        \App\Models\SiswaDataLJK::withoutGlobalScopes()
                            ->where('id', $record->id)
                            ->update([
                                'cekal_ujian_uts'   => 'N',
                                'cekal_ujian_uas'   => 'N',
                                'jml_pelanggaran_uts' => 0,
                                'jml_pelanggaran_uas' => 0,
                            ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Akses Ujian Direset')
                            ->body('Mahasiswa dapat kembali mengakses ujian.')
                            ->success()
                            ->send();
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
