<?php

namespace App\Filament\Resources\DosenData\Pages;

use App\Filament\Resources\DosenData\DosenDataResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDosenData extends CreateRecord
{
    protected static string $resource = DosenDataResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Determine User Name
        $userName = $data['username_account'] ?? ($data['nama'] ?? 'User-' . time());

        // 2. Determine Email
        $email = $data['email_account'] ?? null;
        if (blank($email)) {
            $baseEmail = strtolower(\Illuminate\Support\Str::slug($userName));
            $email = $baseEmail . rand(1000, 9999) . '@dosen.siakad.com';
        }

        // 3. Determine Password
        $password = $data['password_account'] ?? 'password';
        if (blank($password)) {
            $password = 'password';
        }

        // 4. Create the User
        $user = \App\Models\User::create([
            'name' => $userName,
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'view_password' => $password,
        ]);

        // Assign Role 'pengajar'
        if (\Spatie\Permission\Models\Role::where('name', 'pengajar')->exists()) {
            $user->assignRole('pengajar');
        }

        // 5. Assign user_id to DosenData
        $data['user_id'] = $user->id;

        // 6. Clean up temporary fields
        unset($data['username_account']);
        unset($data['email_account']);
        unset($data['password_account']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // kembali ke list page
    }
}
