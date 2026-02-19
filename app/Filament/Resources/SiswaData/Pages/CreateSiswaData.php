<?php

namespace App\Filament\Resources\SiswaData\Pages;

use App\Filament\Resources\SiswaData\SiswaDataResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSiswaData extends CreateRecord
{
    protected static string $resource = SiswaDataResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Determine User Name
        // Priority: username_account -> nama -> nama_lengkap -> Default
        $userName = $data['username_account'] ?? ($data['nama'] ?? ($data['nama_lengkap'] ?? 'User-' . time()));

        // 2. Determine Email
        // Priority: email_account -> Auto-generated
        $email = $data['email_account'] ?? null;
        if (blank($email)) {
            $baseEmail = strtolower(\Illuminate\Support\Str::slug($userName));
            $email = $baseEmail . rand(1000, 9999) . '@student.siakad.com';
        }

        // 3. Determine Password
        // Priority: password_account -> Default 'password'
        $password = $data['password_account'] ?? 'password';
        if (blank($password)) {
            $password = 'password';
        }

        // 4. Create the User
        // TODO: Handle unique constraint violation gracefully if needed
        $user = \App\Models\User::create([
            'name' => $userName,
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
        ]);

        // Assign Role 'murid'
        if (\Spatie\Permission\Models\Role::where('name', 'murid')->exists()) {
            $user->assignRole('murid');
        }

        // 5. Assign user_id to SiswaData
        $data['user_id'] = $user->id;

        // 6. Clean up temporary fields so SiswaData creation doesn't fail
        unset($data['username_account']);
        unset($data['email_account']);
        unset($data['password_account']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        // Create SiswaDataOrangTua if not exists
        \App\Models\SiswaDataOrangTua::firstOrCreate(
            ['id_siswa_data' => $record->id],
        );

        // Create SiswaDataPendaftar if not exists
        \App\Models\SiswaDataPendaftar::firstOrCreate(
            ['id_siswa_data' => $record->id],
            [
                'Status_Pendaftar' => 'Y', // Menambahkan status pendaftar dengan nilai 'Y'
            ]
        );
    }
}
