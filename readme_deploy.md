# Panduan Lengkap Deployment & Instalasi SIAKAD

Panduan ini berisi langkah-langkah lengkap dari awal instalasi, setup database, konfigurasi roles, hingga aplikasi siap dijalankan.

## 🛠️ Persyaratan Sistem (Prerequisites)

Pastikan server atau komputer lokal Anda telah terinstal perangkat lunak berikut:

- **PHP** >= 8.1 (Direkomendasikan 8.2+)
- **Composer** (v2.x)
- **Node.js** & **NPM** (Versi LTS)
- **MySQL** atau **MariaDB**
- Web Server (Apache/Nginx/Herd) untuk mode production

---

## 🚀 Langkah-langkah Instalasi

### 1. Dapatkan Source Code / Clone Repository

Pastikan Anda sudah berada di dalam folder project melalui terminal.

```bash
git clone <url-repo-aplikasi>
cd siakad
```

_(Lewati langkah ini jika Anda sudah memiliki folder project di lokal)_

### 2. Install Dependensi PHP (Composer)

Download semua package Laravel (termasuk Filament dan plugin lainnya) yang dibutuhkan.

```bash
composer install
```

### 3. Install Dependensi Frontend (NPM)

Instal dependensi untuk keperluan aset antarmuka (Tailwind CSS, Vite, dsb).

```bash
npm install
```

### 4. Konfigurasi Environment

Buat file environment dengan menyalin dari `.env.example`.

```bash
cp .env.example .env
```

Buka file `.env` di text editor (VS Code, dll) lalu atur konfigurasi ke database yang sudah disiapkan:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siakad_db  # Sesuaikan dengan nama database Anda
DB_USERNAME=root       # Sesuaikan dengan username mysql Anda
DB_PASSWORD=           # Isi jika mysql Anda memiliki password
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Storage Link

Kaitkan direktori penyimpanan agar file media (foto profil siswa, dosen, dsb) dapat diakses melalui browser:

```bash
php artisan storage:link
```

### 7. Migrasi & Seeding Database (Penting!)

Tahap ini akan mengeksekusi file migrasi untuk mendefinisikan struktur tabel, kemudian menjalankan file `DatabaseSeeder.php` di mana kita hanya mengatur **Role (Filament Shield)** dan **1 Akun Super Admin**.

```bash
php artisan migrate:fresh --seed
```

**Informasi Akun Default Admin yang akan dibuat:**

- **Email:** admin@admin.com
- **Password:** password
  _(Sangat disarankan untuk mengubah password stelah Anda berhasil login)_

### 8. Build Aset Frontend

Lakukan proses build untuk menyiapkan file CSS dan JavaScript final:

```bash
npm run build
```

_(Untuk development, Anda juga bisa memakai `npm run dev`)_

### 9. Jalankan Aplikasi

Jika berjalan di PC lokal untuk tahap pengembangan:

```bash
php artisan serve
```

Aplikasi kini bisa diakses melalui URL: `http://localhost:8000` atau `http://localhost:8000/admin` (Tergantung prefix filament Anda).

---

## 🛠️ Memperbaiki Error `bootstrap/cache`

Jika Anda menemui error saat akses aplikasi (blank page atau error terkait routing/config), biasanya disebabkan oleh cache yang korup atau tidak sinkron. Jalankan perintah berikut:

### 1. Hapus Semua Cache

```bash
php artisan optimize:clear
```

Perintah ini akan menghapus:

- Cache aplikasi (`cache:clear`)
- Cache rute (`route:clear`)
- Cache konfigurasi (`config:clear`)
- Cache file view (`view:clear`)

### 2. Cara Manual (Jika cara di atas gagal)

Hapus secara manual semua file di dalam folder `bootstrap/cache/` **kecuali** file `.gitignore`.

```powershell
# Untuk Windows (PowerShell)
Remove-Item -Path "bootstrap/cache/*.php" -Exclude ".gitignore"
```

---

## 🛡️ Mengelola Roles & Permissions (Filament Shield)

Aplikasi ini menggunakan **Filament Shield** untuk menangani otorisasi, hak akses (permissions), dan peran (roles).

### Penting: Agar Tidak "Centang Ulang" Role Murid/Pengajar

Agar Anda tidak perlu menceklis ulang hak akses di menu Roles setiap kali melakukan deployment atau seeder ulang:

1. Atur semua permission di Panel Admin (Menu Roles -> Edit).
2. Setelah selesai, jalankan perintah ini di terminal:
    ```bash
    php artisan shield:seed
    ```
    Perintah ini akan memperbarui file `database/seeders/ShieldSeeder.php` dengan pilihan centang (permissions) yang baru saja Anda buat.
3. Commit dan push file `ShieldSeeder.php` tersebut ke repository.

**Perintah untuk men-generate/update seluruh permission (Otomatis deteksi module baru):**

```bash
php artisan shield:generate --all
```

🎉 **Selesai! Aplikasi sudah siap dioperasikan dengan bersih hanya menyisakan Super Admin.**
