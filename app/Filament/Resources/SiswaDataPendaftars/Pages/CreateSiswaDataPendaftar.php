<?php

namespace App\Filament\Resources\SiswaDataPendaftars\Pages;

use App\Filament\Resources\SiswaDataPendaftars\SiswaDataPendaftarResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use App\Models\SiswaData;
use App\Models\SiswaDataOrangTua;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateSiswaDataPendaftar extends CreateRecord
{
    protected static string $resource = SiswaDataPendaftarResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['is_new_siswa']) && $data['is_new_siswa'] === true) {
            DB::transaction(function () use (&$data) {
                // 1. Create User
                $user = User::create([
                    'name' => $data['new_nama'],
                    'email' => $data['new_username'],
                    'password' => Hash::make($data['new_password']),
                    'view_password' => $data['new_password'],
                ]);

                // Assign role pendaftar
                $user->assignRole('pendaftar');

                // 2. Create Siswa Data
                $siswaData = SiswaData::create([
                    'nama' => $data['new_nama'],
                    'nama_lengkap' => $data['new_nama'],
                    'user_id' => $user->id, // Set user_id here!
                    'email' => $data['new_username'],
                    'jenis_kelamin' => $data['new_jenis_kelamin'] ?? null,
                    'kota_lahir' => $data['new_tempat_lahir'] ?? null,
                    'tanggal_lahir' => $data['new_tanggal_lahir'] ?? null,
                    'no_telepon' => $data['new_no_telepon'] ?? null,
                    'alamat' => $data['new_alamat'] ?? null,
                    'kewarganegaraan' => 'WNI',
                ]);

                // 3. Create placeholder Orangtua
                SiswaDataOrangTua::create([
                    'id_siswa_data' => $siswaData->id,
                ]);

                // Assign to current Pendaftar
                $data['id_siswa_data'] = $siswaData->id;
            });
        }

        // Clean up unmapped pseudo-fields so Filament doesn't try to insert them into DB
        unset(
            $data['is_new_siswa'],
            $data['new_nama'],
            $data['new_username'],
            $data['new_password'],
            $data['new_jenis_kelamin'],
            $data['new_tempat_lahir'],
            $data['new_tanggal_lahir'],
            $data['new_no_telepon'],
            $data['new_alamat']
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Otomatis buat Program Seleksi (Tahap 1 & 2)
        \App\Models\SiswaSeleksiPendaftar::create([
            'id_siswa_data_pendaftar' => $record->id,
            'nama_seleksi' => 'Verifikasi Administrasi & Tes Tulis',
            'tanggal_seleksi' => now()->addDays(2),
            'deskripsi_seleksi' => 'Silakan datang ke kampus sesuai jadwal untuk mengikuti tes tulis dan membawa dokumen fisik.',
            'status_seleksi' => 'B',
        ]);

        \App\Models\SiswaSeleksiPendaftar::create([
            'id_siswa_data_pendaftar' => $record->id,
            'nama_seleksi' => 'Wawancara & Portofolio',
            'tanggal_seleksi' => now()->addDays(4),
            'deskripsi_seleksi' => 'Wawancara dilakukan secara online/offline. Silakan siapkan berkas jurnal jika diminta.',
            'status_seleksi' => 'B',
        ]);
    }
}
