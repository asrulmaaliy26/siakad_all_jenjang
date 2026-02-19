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

        // Check if the record doesn't have a user_id yet
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
            ]);

            // Assign Role 'pengajar'
            if (\Spatie\Permission\Models\Role::where('name', 'pengajar')->exists()) {
                $user->assignRole('pengajar');
            }

            // 5. Link user to the record
            $data['user_id'] = $user->id;
        }

        // Clean up temporary fields
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
