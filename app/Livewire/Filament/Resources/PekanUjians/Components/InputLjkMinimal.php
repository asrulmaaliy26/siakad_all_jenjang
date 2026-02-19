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
        if ($user && $user->hasRole('murid')) {
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
                // Form is dynamically loaded based on selection, but defined here
                FileUpload::make($this->type == 'uas' ? 'ljk_uas' : 'ljk_uts')
                    ->label('File LJK ' . strtoupper($this->type))
                    ->disk('public')
                    ->directory(fn($record) => \App\Helpers\UploadPathHelper::uploadUjianPath($record, $this->type == 'uas' ? 'ljk_uas' : 'ljk_uts', 'siswa'))
                    ->visibility('public')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(10240)
                    ->downloadable()
                    ->openable()
                    ->columnSpanFull(),
                RichEditor::make($this->type == 'uas' ? 'ctt_uas' : 'ctt_uts') // Note field might be different in DB?
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
