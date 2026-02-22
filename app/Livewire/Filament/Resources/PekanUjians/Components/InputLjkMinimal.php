<?php

namespace App\Livewire\Filament\Resources\PekanUjians\Components;

use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Models\SiswaDataLJK;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;

class InputLjkMinimal extends Component implements HasForms
{
    use InteractsWithForms;

    public ?Model $record = null; // MataPelajaranKelas
    public string $type = 'uts'; // 'uts' or 'uas'
    public ?string $selectedStudentId = null;
    public ?array $data = [];

    public function mount($record, $type)
    {
        try {
            $this->record = $record;
            $this->type = $type;

            /** @var \App\Models\User $user */
            $user = \Filament\Facades\Filament::auth()->user();
            if ($user && $user->isMurid()) {
                $siswa = \App\Models\SiswaData::where('user_id', $user->id)->first();
                if ($siswa) {
                    $this->selectedStudentId = $siswa->id;
                    $this->updatedSelectedStudentId();
                }
            } else {
                // Ensure admins/teachers start with a fresh selection
                $this->selectedStudentId = null;
                $this->data = [];
                $this->form->fill([]);
            }
        } catch (\Exception $e) {
            Log::error('Error in InputLjkMinimal mount: ' . $e->getMessage());
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Soal ' . strtoupper($this->type))
                    ->schema([
                        Placeholder::make('download_soal')
                            ->label('File Soal')
                            ->content(function () {
                                $field = $this->type == 'uas' ? 'soal_uas' : 'soal_uts';
                                $file = $this->record?->$field;
                                if (!$file) return 'Tidak ada file soal.';
                                return new \Illuminate\Support\HtmlString('<a href="' . asset('storage/' . $file) . '" target="_blank" class="text-primary-600 underline font-bold">Unduh / Lihat Soal</a>');
                            }),

                        Placeholder::make('catatan_soal')
                            ->label('Instruksi / Soal Text')
                            ->content(function () {
                                $field = $this->type == 'uas' ? 'ctt_soal_uas' : 'ctt_soal_uts';
                                $text = $this->record?->$field;
                                return new \Illuminate\Support\HtmlString($text ?? '-');
                            }),
                    ])
                    ->collapsible(),

                Section::make('Input Jawaban ' . strtoupper($this->type))
                    ->schema([
                        FileUpload::make($this->type == 'uas' ? 'ljk_uas' : 'ljk_uts')
                            ->label('Upload Jawaban LJK ' . strtoupper($this->type))
                            ->disk('public')
                            ->directory(fn($get, $record) => \App\Helpers\UploadPathHelper::uploadUjianPath($get, $record, $this->type == 'uas' ? 'ljk_uas' : 'ljk_uts'))
                            ->visibility('public')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),

                        RichEditor::make($this->type == 'uas' ? 'ctt_uas' : 'ctt_uts')
                            ->label('Catatan / Jawaban Text')
                            ->columnSpanFull(),
                    ])
            ])
            ->statePath('data')
            ->model($this->getSelectedLjkRecord() ?? SiswaDataLJK::class);
    }

    public function getSelectedLjkRecord()
    {
        if (!$this->selectedStudentId) return null;

        try {
            $record = SiswaDataLJK::query()
                ->withoutGlobalScopes()
                ->where('id_mata_pelajaran_kelas', $this->record?->id)
                ->whereHas('akademikKrs', function ($q) {
                    // Critical: bypass scope for the KRS record itself
                    $q->withoutGlobalScopes()->whereHas('riwayatPendidikan', function ($sq) {
                        // Critical: bypass scope for the Education History record
                        $sq->withoutGlobalScopes()->where('id_siswa_data', $this->selectedStudentId);
                    });
                })
                ->first();

            if ($record) {
                Log::info("LJK record found for student ID: {$this->selectedStudentId}", ['id' => $record->id]);
            } else {
                Log::warning("LJK record NOT found for student ID: {$this->selectedStudentId} in MK: {$this->record?->id}");
            }

            return $record;
        } catch (\Exception $e) {
            Log::error('Error fetching LJK record: ' . $e->getMessage());
            return null;
        }
    }

    public function updatedSelectedStudentId()
    {
        try {
            $ljk = $this->getSelectedLjkRecord();
            if ($ljk) {
                // Explicitly set the public data property and fill the form
                $this->data = $ljk->attributesToArray();
                $this->form->fill($this->data);

                Log::info("Form filled for student {$this->selectedStudentId}", ['data_keys' => array_keys($this->data)]);

                Notification::make()
                    ->title('Data Pelajar Dimuat')
                    ->body('Data LJK untuk mahasiswa tersebut berhasil ditemukan.')
                    ->success()
                    ->send();
            } else {
                $this->data = [];
                $this->form->fill([]);
                if ($this->selectedStudentId) {
                    Notification::make()
                        ->title('Data Tidak Ditemukan')
                        ->body('Data LJK belum dibuat untuk mahasiswa ini di mata pelajaran ini.')
                        ->warning()
                        ->send();
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in updatedSelectedStudentId: ' . $e->getMessage());
            Notification::make()
                ->title('Gagal Memuat Data')
                ->body('Terjadi kesalahan saat mengambil data: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function save()
    {
        try {
            $ljk = $this->getSelectedLjkRecord();
            if (!$ljk) {
                Notification::make()
                    ->title('Gagal Menyimpan')
                    ->body('Data LJK tidak ditemukan untuk mata pelajaran ini.')
                    ->danger()
                    ->send();
                return;
            }

            // Validasi data form
            $state = $this->form->getState();

            if (empty($state)) {
                Notification::make()
                    ->title('Peringatan')
                    ->body('Tidak ada data yang diubah atau data form tidak valid.')
                    ->warning()
                    ->send();
                return;
            }

            // Update timestamp upload jika ada file baru
            $fileField = $this->type == 'uas' ? 'ljk_uas' : 'ljk_uts';
            $tglField = $this->type == 'uas' ? 'tgl_upload_ljk_uas' : 'tgl_upload_ljk_uts';

            // Khusus pengecekan jika file benar-benar berubah
            if (isset($state[$fileField]) && $state[$fileField] !== $ljk->$fileField) {
                $state[$tglField] = now();
            }

            // Lakukan update (menggunakan withoutGlobalScopes untuk keamanan simpan lintas jenjang)
            $success = SiswaDataLJK::query()
                ->withoutGlobalScopes()
                ->where('id', $ljk->id)
                ->update($state);

            if ($success) {
                Notification::make()
                    ->title('Berhasil Disimpan')
                    ->body('Data Jawaban LJK ' . strtoupper($this->type) . ' telah berhasil diperbarui.')
                    ->success()
                    ->send();

                $this->updatedSelectedStudentId(); // Refresh form state
            } else {
                throw new \Exception('Update gagal dilakukan ke database.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Notification::make()
                ->title('Kesalahan Validasi')
                ->body('Pastikan semua input sudah benar: ' . implode(', ', $e->errors()))
                ->danger()
                ->send();
        } catch (\Exception $e) {
            Log::error('Error saving LJK: ' . $e->getMessage(), [
                'student_id' => $this->selectedStudentId,
                'mk_id' => $this->record?->id,
                'trace' => $e->getTraceAsString()
            ]);

            Notification::make()
                ->title('Terjadi Kesalahan')
                ->body('Gagal menyimpan data: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.filament.resources.pekan-ujians.components.input-ljk-minimal');
    }
}
