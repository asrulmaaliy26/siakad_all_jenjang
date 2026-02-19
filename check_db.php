<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$table = 'mata_pelajaran_kelas';
if (Schema::hasTable($table)) {
    $columns = DB::select("DESCRIBE $table");
    foreach ($columns as $column) {
        echo "{$column->Field} ({$column->Type})\n";
    }
} else {
    echo "Table $table does not exist.\n";
}
