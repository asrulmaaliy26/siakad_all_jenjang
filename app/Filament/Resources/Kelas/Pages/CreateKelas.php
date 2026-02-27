<?php

namespace App\Filament\Resources\Kelas\Pages;

use App\Filament\Resources\Kelas\KelasResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKelas extends CreateRecord
{
    protected static string $resource = KelasResource::class;
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $programs = (array) ($data['ro_program_kelas'] ?? []);
        $semesters = (array) ($data['semester'] ?? []);

        $lastRecord = null;

        foreach ($programs as $prog) {
            foreach ($semesters as $sem) {
                $lastRecord = static::getModel()::create([
                    'ro_program_kelas' => $prog,
                    'semester' => $sem,
                    'id_tahun_akademik' => $data['id_tahun_akademik'] ?? null,
                    'id_jurusan' => $data['id_jurusan'] ?? null,
                    'status_aktif' => $data['status_aktif'] ?? 'Y',
                ]);
            }
        }

        return $lastRecord;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // kembali ke list page
    }
}
