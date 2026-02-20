# SIAKAD Application

## Persyaratan

- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL/MariaDB

## Instalasi

1. **Clone Repository**

    ```bash
    git clone <repository_url>
    cd siakad
    ```

2. **Install Dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Konfigurasi Environment**
   Salin file `.env.example` ke `.env` dan sesuaikan konfigurasi database Anda.

    ```bash
    cp .env.example .env
    ```

    Atur koneksi database di `.env`:

    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=siakad
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4. **Generate Key**

    ```bash
    php artisan key:generate
    ```

5. **Setup Database**

    Aplikasi ini membutuhkan struktur database yang spesifik.

    a. Buat database baru (misal: `siakad`).

    b. Jalankan migrasi dasar Laravel (untuk tabel users, jobs, dll):

    ```bash
    php artisan migrate
    ```

    c. **PENTING**: Import file SQL database (misalnya `database.sql`) ke dalam database Anda. File ini berisi struktur tabel aplikasi utama.
    Anda dapat menggunakan tool seperti phpMyAdmin, DBeaver, atau lewat terminal:

    ```bash
    mysql -u root -p siakad < database.sql
    ```

6. **Setup Storage**
   Agar file upload (seperti foto profil, LJK) dapat diakses:

    ```bash
    php artisan storage:link
    ```

7. **Build Assets**
    ```bash
    npm run build
    ```

## Setup Filament & Filament Shield

Aplikasi ini menggunakan Filament untuk panel admin dan Filament Shield untuk manajemen hak akses (Roles & Permissions).

### Default Setup (Jika menggunakan database dump lengkap)

Jika Anda mengimport database yang sudah berisi data user dan roles:

1. Login menggunakan akun administrator yang sudah ada.
2. Akses panel di `/admin`.

### Fresh Setup (Jika database kosong/baru)

1. **Install Filament Shield**
   Jika tabel permissions belum ada atau Anda ingin meresetnya:

    ```bash
    php artisan shield:install
    ```

    Langkah ini akan membuat role `super_admin` dan permission dasar.

2. **Generate Permissions**
   Generate permission untuk semua resource dan page yang ada:

    ```bash
    php artisan shield:generate --all
    ```

3. **Buat User Super Admin**
   Buat user baru dan berikan role `super_admin`:

    ```bash
    php artisan make:filament-user
    ```

    Ikuti langkah-langkah di terminal. Setelah user dibuat, assign role melalui database atau jika user pertama, biasanya Filament Shield menjadikannya Super Admin.

    Alternatif via Tinker:

    ```bash
    php artisan tinker
    ```

    ```php
    $user = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('password')
    ]);
    $user->assignRole('super_admin');
    ```

## Menjalankan Aplikasi

Jalankan server lokal:

```bash
php artisan serve
```

Akan berjalan di `http://127.0.0.1:8000`. Panel admin di `http://127.0.0.1:8000/admin`.
