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
        $this->record = $record;
        $this->type = $type;

        $user = \Filament\Facades\Filament::auth()->user();
        if ($user && $user->isMurid()) {
            $siswa = \App\Models\SiswaData::where('user_id', $user->id)->first();
            if ($siswa) {
                $this->selectedStudentId = $siswa->id;
                $this->updatedSelectedStudentId();
            }
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Soal ' . strtoupper($this->type))
                    ->schema([
                        Placeholder::make('download_soal')
                            ->label('File Soal')
                            ->content(function () {
                                $field = $this->type == 'uas' ? 'soal_uas' : 'soal_uts';
                                $file = $this->record->$field;
                                if (!$file) return 'Tidak ada file soal.';
                                return new \Illuminate\Support\HtmlString('<a href="' . asset('storage/' . $file) . '" target="_blank" class="text-primary-600 underline font-bold">Unduh / Lihat Soal</a>');
                            }),

                        Placeholder::make('catatan_soal')
                            ->label('Instruksi / Soal Text')
                            ->content(function () {
                                $field = $this->type == 'uas' ? 'ctt_soal_uas' : 'ctt_soal_uts';
                                $text = $this->record->$field;
                                return new \Illuminate\Support\HtmlString($text ?? '-');
                            }),
                    ])
                    ->collapsible(),

                // Form is dynamically loaded based on selection, but defined here
                FileUpload::make($this->type == 'uas' ? 'ljk_uas' : 'ljk_uts')
                    ->label('Upload Jawaban LJK ' . strtoupper($this->type))
                    ->disk('public')
                    // Fix: Pass $get and $record correctly, and ensure correct argument order for uploadUjianPath
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
            ->statePath('data')
            ->model($this->getSelectedLjkRecord() ?? SiswaDataLJK::class);
    }

    public function getSelectedLjkRecord()
    {
        if (!$this->selectedStudentId) return null;

        // Find SiswaDataLJK for this student (via AkademikKrs logic)
        // We iterate record->siswaDataLjk to find the one matching the student ID
        return $this->record->siswaDataLjk->first(function ($ljk) {
            return $ljk->akademikKrs?->riwayatPendidikan?->siswaData?->id == $this->selectedStudentId;
        });
    }

    public function updatedSelectedStudentId()
    {
        $ljk = $this->getSelectedLjkRecord();
        if ($ljk) {
            $this->form->fill($ljk->toArray());
        } else {
            $this->form->fill([]);
        }
    }

    public function save()
    {
        $ljk = $this->getSelectedLjkRecord();
        if (!$ljk) return;

        $state = $this->form->getState();
        $ljk->update($state);

        Notification::make()
            ->title('Data LJK Berhasil Disimpan')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.filament.resources.pekan-ujians.components.input-ljk-minimal');
    }
}
