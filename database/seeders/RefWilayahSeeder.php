<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RefWilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat tabel jika belum ada (bypass migration agar aman)
        \Illuminate\Support\Facades\DB::statement("CREATE TABLE IF NOT EXISTS ref_wilayahs (id_wil VARCHAR(50) PRIMARY KEY, kecamatan VARCHAR(255) NULL, kabupaten VARCHAR(255) NULL, provinsi VARCHAR(255) NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)");
        
        // 2. Import Excel
        $filePath = base_path('pddikti_template/ref_wilayah_OpenFeeder.xlsx');
        if (!file_exists($filePath)) {
            $this->command->error("File excel tidak ditemukan: $filePath");
            return;
        }

        $this->command->info("Membaca file excel...");
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        
        $data = [];
        $count = 0;
        
        $this->command->info("Mengimport data wilayah...");
        for ($row = 2; $row <= $highestRow; $row++) {
            $id_wil = trim((string)$worksheet->getCell("A".$row)->getValue());
            $kecamatan = trim((string)$worksheet->getCell("B".$row)->getValue());
            $kabupaten = trim((string)$worksheet->getCell("C".$row)->getValue());
            $provinsi = trim((string)$worksheet->getCell("D".$row)->getValue());
            
            if(!empty($id_wil)) {
                $data[] = [
                    "id_wil" => $id_wil, 
                    "kecamatan" => $kecamatan, 
                    "kabupaten" => $kabupaten, 
                    "provinsi" => $provinsi,
                    "created_at" => now(),
                    "updated_at" => now()
                ];
                
                if(count($data) >= 500) {
                    \Illuminate\Support\Facades\DB::table("ref_wilayahs")->insertOrIgnore($data);
                    $count += count($data);
                    $data = [];
                }
            }
        }
        
        if(count($data) > 0) {
            \Illuminate\Support\Facades\DB::table("ref_wilayahs")->insertOrIgnore($data);
            $count += count($data);
        }
        
        $this->command->info("Berhasil import $count baris kecamatan!");
    }
}
