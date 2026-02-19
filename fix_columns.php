<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    DB::statement("ALTER TABLE mata_pelajaran_kelas 
        ADD COLUMN ro_pelaksanaan_kelas bigint(20) unsigned DEFAULT NULL AFTER ro_ruang_kelas,
        ADD COLUMN id_pengawas bigint(20) unsigned DEFAULT NULL AFTER ro_pelaksanaan_kelas,
        ADD COLUMN jumlah int(11) DEFAULT 0 AFTER id_pengawas,
        ADD COLUMN hari varchar(50) DEFAULT NULL AFTER jumlah,
        ADD COLUMN tanggal date DEFAULT NULL AFTER hari,
        ADD COLUMN jam varchar(50) DEFAULT NULL AFTER tanggal,
        ADD COLUMN tgl_uts date DEFAULT NULL AFTER jam,
        ADD COLUMN tgl_uas date DEFAULT NULL AFTER tgl_uts,
        ADD COLUMN status_uts enum('Y','N') DEFAULT 'N' AFTER tgl_uas,
        ADD COLUMN status_uas enum('Y','N') DEFAULT 'N' AFTER status_uts,
        ADD COLUMN ruang_uts varchar(100) DEFAULT NULL AFTER status_uas,
        ADD COLUMN ruang_uas varchar(100) DEFAULT NULL AFTER ruang_uts,
        ADD COLUMN link_kelas text DEFAULT NULL AFTER ruang_uas,
        ADD COLUMN passcode varchar(100) DEFAULT NULL AFTER link_kelas");
    
    echo "Columns added successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
