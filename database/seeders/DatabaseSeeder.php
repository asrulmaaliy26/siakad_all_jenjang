<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🧹 Membersihkan data yang ada jika diperlukan...');

        $this->command->info('🚀 Memulai seeding profil default...');

        // 1. Menjalankan Shield Seeder untuk Men-generate Role & Permission (termasuk super_admin)
        $this->command->info('⚙️ Memuat dan Menulis Roles & Permissions (Shield)...');
        $this->call(ShieldSeeder::class);

        // 2. Membuat User Super Admin Pertama
        $this->command->info('👤 Membuat user Super Admin...');
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'view_password' => 'password',
            ]
        );

        // 3. Memastikan user admin mendapatkan role super_admin secara benar
        if (!$admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }

        $this->command->info('');
        $this->command->info('✅ Setup Default SIAKAD Berhasil!');
        $this->command->info('----------------------------------------------');
        $this->command->info('🔑 Email login : admin@admin.com');
        $this->command->info('🔑 Password    : password');
        $this->command->info('----------------------------------------------');
        $this->command->info('🎉 Seeding Selesai. Semua dummy data yang tidak diperlukan telah dihapus dari seeder ini.');
    }
}
