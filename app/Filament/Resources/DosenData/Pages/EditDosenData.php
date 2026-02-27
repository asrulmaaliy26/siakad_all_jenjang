<?php

namespace App\Filament\Resources\DosenData\Pages;

use App\Filament\Resources\DosenData\DosenDataResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDosenData extends EditRecord
{
    protected static string $resource = DosenDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        // ── KASUS 1: Belum punya akun, buat baru ─────────────────────────────
        if ($record->user_id === null && (isset($data['username_account']) || isset($data['email_account']))) {

            // 1. Determine User Name
            $userName = $data['username_account'] ?? ($record->nama ?? 'User-' . time());

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

            // 5. Link user to the record
            $data['user_id'] = $user->id;
        }

        // ── KASUS 2: Sudah ada akun, update data akun ────────────────────────
        if ($record->user_id !== null) {
            $user = \App\Models\User::find($record->user_id);
            if ($user) {
                $userData = [];
                if (isset($data['user_name']) && !blank($data['user_name'])) {
                    $userData['name'] = $data['user_name'];
                }
                if (isset($data['user_email']) && !blank($data['user_email'])) {
                    $userData['email'] = $data['user_email'];
                }
                if (isset($data['user_password']) && !blank($data['user_password'])) {
                    $userData['password'] = \Illuminate\Support\Facades\Hash::make($data['user_password']);
                    $userData['view_password'] = $data['user_password'];
                }

                if (!empty($userData)) {
                    $user->update($userData);
                }
            }
        }

        // Clean up temporary fields
        unset($data['username_account']);
        unset($data['email_account']);
        unset($data['password_account']);
        unset($data['user_name']);
        unset($data['user_email']);
        unset($data['user_password']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // kembali ke list page
    }
}
