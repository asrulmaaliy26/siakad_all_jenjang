<?php

namespace App\Filament\Resources\SiswaData\Pages;

use App\Filament\Resources\SiswaData\SiswaDataResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSiswaData extends EditRecord
{
    protected static string $resource = SiswaDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $user = $record->user;

        if ($user) {
            $data['username_account'] = $user->name;
            $data['email_account'] = $user->email;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();
        $user = $record->user;

        // If user doesn't exist, create it
        if (!$user && (isset($data['username_account']) || isset($data['email_account']))) {
            $userName = $data['username_account'] ?? ($data['nama'] ?? ($data['nama_lengkap'] ?? 'User-' . time()));

            $email = $data['email_account'] ?? null;
            if (blank($email)) {
                $baseEmail = strtolower(\Illuminate\Support\Str::slug($userName));
                $email = $baseEmail . rand(1000, 9999) . '@student.siakad.com';
            }

            $password = $data['password_account'] ?? 'password';
            if (blank($password)) {
                $password = 'password';
            }

            $user = \App\Models\User::create([
                'name' => $userName,
                'email' => $email,
                'password' => \Illuminate\Support\Facades\Hash::make($password),
                'view_password' => $password,
            ]);

            // Assign user_id to SiswaData
            $data['user_id'] = $user->id;
        }

        // Ensure user has 'murid' role if user exists
        if ($user) {
            if (\Spatie\Permission\Models\Role::where('name', 'murid')->exists()) {
                if (!$user->hasRole('murid')) {
                    $user->assignRole('murid');
                }
            }

            // Optional: Update password if password_account is provided
            if (!empty($data['password_account'])) {
                $user->update([
                    'password' => \Illuminate\Support\Facades\Hash::make($data['password_account']),
                    'view_password' => $data['password_account'],
                ]);
            }
        }

        // Clean up temporary fields
        unset($data['username_account']);
        unset($data['email_account']);
        unset($data['password_account']);

        return $data;
    }
    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index'); // kembali ke list page
    // }
}
