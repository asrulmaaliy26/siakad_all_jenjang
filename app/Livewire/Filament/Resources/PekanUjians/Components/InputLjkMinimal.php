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

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;

class InputLjkMinimal extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public function boot()
    {
        Log::info("Booting InputLjkMinimal", [
            'id' => $this->getId(),
            'user' => auth()->id(),
            'type' => $this->type ?? 'not set',
            'has_record' => isset($this->record),
        ]);
    }

    public function hydrate()
    {
        Log::info("Hydrating InputLjkMinimal", [
            'id' => $this->getId(),
            'selectedStudentId' => $this->selectedStudentId
        ]);
    }

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
        $ljkField = $this->type == 'uas' ? 'ljk_uas' : 'ljk_uts';
        $cttField = $this->type == 'uas' ? 'ctt_uas' : 'ctt_uts';

        return $form
            ->components([
                Section::make('Soal ' . strtoupper($this->type))
                    ->schema([
                        Placeholder::make('download_soal')
                            ->label('File Soal')
                            ->content(function () {
                                $field = $this->type == 'uas' ? 'soal_uas' : 'soal_uts';
                                $fileValue = $this->record?->$field;
                                $file = is_array($fileValue) ? ($fileValue[0] ?? null) : $fileValue;
                                if (!$file) return 'Tidak ada file soal.';
                                return new \Illuminate\Support\HtmlString('<a href="' . asset('storage/' . $file) . '" target="_blank" class="text-primary-600 underline font-bold">Unduh / Lihat Soal</a>');
                            }),

                        // Placeholder::make('catatan_soal')
                        //     ->label('Instruksi / Soal Text')
                        //     ->content(function () {
                        //         $field = $this->type == 'uas' ? 'ctt_soal_uas' : 'ctt_soal_uts';
                        //         $text = $this->record?->$field;
                        //         return new \Illuminate\Support\HtmlString($text ?? '-');
                        //     }),
                    ])
                    ->collapsible(),

                Section::make('Input Jawaban ' . strtoupper($this->type))
                    ->schema([
                        // Show already-uploaded files as clickable links so students can verify their submissions
                        Placeholder::make('file_tersimpan_' . $ljkField)
                            ->label('File Jawaban Tersimpan')
                            ->content(function () use ($ljkField) {
                                $ljk = $this->getSelectedLjkRecord();
                                if (!$ljk) return new \Illuminate\Support\HtmlString('<span class="text-gray-400 italic">Belum ada file tersimpan.</span>');

                                $dir = \App\Helpers\UploadPathHelper::uploadUjianPath(null, $ljk, $ljkField);
                                $files = \Illuminate\Support\Facades\Storage::disk('public')->files($dir);
                                if (empty($files)) return new \Illuminate\Support\HtmlString('<span class="text-gray-400 italic">Belum ada file tersimpan.</span>');

                                $html = '<div class="flex flex-col gap-2">';
                                foreach ($files as $filePath) {
                                    if (!$filePath) continue;
                                    $url = asset('storage/' . $filePath);
                                    $name = basename($filePath);
                                    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                    $icon = in_array($ext, ['pdf']) ? '📄' : (in_array($ext, ['doc', 'docx']) ? '📝' : '🖼️');
                                    $html .= '<a href="' . $url . '" target="_blank" '
                                        . 'class="inline-flex items-center gap-2 px-3 py-2 bg-green-50 dark:bg-green-900/20 '
                                        . 'border border-green-200 dark:border-green-700 rounded-lg text-sm font-medium '
                                        . 'text-green-700 dark:text-green-300 hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors">';
                                    $html .= '<span>' . $icon . '</span>';
                                    $html .= '<span class="truncate max-w-xs">' . htmlspecialchars($name) . '</span>';
                                    $html .= '<span class="ml-auto text-green-500 text-xs">↗ Lihat / Unduh</span>';
                                    $html .= '</a>';
                                }
                                $html .= '</div>';
                                return new \Illuminate\Support\HtmlString($html);
                            })
                            ->columnSpanFull(),

                        FileUpload::make($ljkField)
                            ->label('Ganti / Upload Ulang File LJK ' . strtoupper($this->type))
                            ->helperText('Upload file baru hanya jika ingin mengganti file yang sudah tersimpan di atas.')
                            ->disk('public')
                            // Pass the LJK record (SiswaDataLJK) as $record so UploadPathHelper
                            // can resolve the student name correctly.
                            ->directory(function ($get) use ($ljkField) {
                                $ljk = $this->getSelectedLjkRecord();
                                return \App\Helpers\UploadPathHelper::uploadUjianPath($get, $ljk, $ljkField);
                            })
                            ->visibility('public')
                            ->multiple()
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/*'])
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),

                        RichEditor::make($cttField)
                            ->label('Catatan / Jawaban Text')
                            ->columnSpanFull(),
                    ])
            ])
            ->statePath('data')
            // Pass the resolved LJK record so Filament can handle file paths correctly
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
                // Use toArray() so model casts (e.g. ljk_uts/ljk_uas cast to array) are applied.
                // attributesToArray() returns raw DB values (JSON string) which FileUpload cannot parse.
                $this->data = $ljk->toArray();
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

    public function submitForm()
    {
        $userId = auth()->id();
        Log::info("submitForm execution started for user: {$userId}");

        try {
            Log::info("Fetching LJK record for student: {$this->selectedStudentId}");
            $ljk = $this->getSelectedLjkRecord();

            if (!$ljk) {
                Log::error("Save failed: LJK record not found for student {$this->selectedStudentId}");
                Notification::make()
                    ->title('Gagal')
                    ->body('Data LJK tidak ditemukan.')
                    ->danger()
                    ->send();
                return;
            }

            Log::info("Getting form state...");
            $state = $this->form->getState();
            Log::info("State retrieved successfully", [
                'keys' => array_keys($state)
            ]);

            // Determine fields
            $fileField = $this->type === 'uas' ? 'ljk_uas' : 'ljk_uts';
            $tglField = $this->type === 'uas' ? 'tgl_upload_ljk_uas' : 'tgl_upload_ljk_uts';

            // Prepare update data
            $updateData = $state;
            if (isset($state[$fileField]) && $state[$fileField] !== $ljk->$fileField) {
                $updateData[$tglField] = now();
            }

            // Ensure array fields are properly JSON encoded for Query Builder update
            // because Query Builder does not use Eloquent's model casts
            $arrayFields = ['ljk_uas', 'ljk_uts', 'artikel_uas', 'artikel_uts'];
            foreach ($arrayFields as $field) {
                if (array_key_exists($field, $updateData)) {
                    $value = $updateData[$field];
                    if ($value) {
                        // If it's a single string, wrap it in array
                        if (is_string($value)) {
                            $value = [$value];
                        }
                        // Encode to JSON array since the DB column expects valid JSON
                        $updateData[$field] = json_encode(array_values($value));
                    } else {
                        $updateData[$field] = null;
                    }
                }
            }

            // Perform Update using the model instance
            Log::info("Updating LJK record via model instance", [
                'id' => $ljk->id,
                'data' => $updateData
            ]);

            $ljk->fill($updateData);
            // $ljk->save(); // We use the instance fetched withoutGlobalScopes

            // To be absolutely sure we bypass scopes during save
            $success = SiswaDataLJK::query()
                ->withoutGlobalScopes()
                ->where('id', $ljk->id)
                ->update($updateData);

            if ($success !== false) { // update() returns number of rows, but can be 0 if nothing changed
                Log::info("Database record updated successfully", ['id' => $ljk->id, 'rows' => $success]);

                Notification::make()
                    ->title('Berhasil Disimpan')
                    ->body('Jawaban Anda telah berhasil diperbarui. File yang tersimpan dapat dilihat di bagian "File Jawaban Tersimpan".')
                    ->success()
                    ->send();

                // Re-fetch fresh record from DB so the form shows the latest saved file paths
                $this->updatedSelectedStudentId(); // Sync data back
            } else {
                Log::error("Update failed for LJK ID: {$ljk->id}");
                throw new \Exception('Gagal memperbarui data ke database.');
            }
        } catch (\Exception $e) {
            Log::error('LJK Save Exception: ' . $e->getMessage(), [
                'student' => $this->selectedStudentId,
                'error' => $e->getMessage()
            ]);

            Notification::make()
                ->title('Gagal Menyimpan')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        Log::info("Rendering InputLjkMinimal", [
            'student' => $this->selectedStudentId,
            'mk_record' => $this->record?->id,
            'has_form_data' => !empty($this->data)
        ]);
        return view('livewire.filament.resources.pekan-ujians.components.input-ljk-minimal');
    }
}
