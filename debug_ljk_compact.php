<?php

use Illuminate\Contracts\Console\Kernel;
use App\Models\SiswaDataLJK;
use Illuminate\Database\Eloquent\Builder;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap(); // Only boot once

$studentName = 'Monroe Cole';

$records = SiswaDataLJK::whereHas('akademikKrs.riwayatPendidikan.siswaData', function ($query) use ($studentName) {
    $query->where('nama', 'like', "%{$studentName}%");
})->get();

echo "Found " . $records->count() . " records for $studentName.\n";

foreach ($records as $record) {
    echo "ID:{$record->id} | MP:{$record->id_mata_pelajaran_kelas}\n";
    $ctt = $record->ctt_uas;
    if (strlen($ctt) > 50) $ctt = substr($ctt, 0, 47) . '...';
    echo "CTT: '" . $ctt . "'\n";

    $stripped = strip_tags($record->ctt_uas ?? '');
    echo "Stripped: '" . trim($stripped) . "'\n";

    $check = (!empty($record->ljk_uas) || (!empty($record->ctt_uas) && trim($stripped) !== ''));
    echo "Result: " . ($check ? 'SHOW' : 'HIDE') . "\n";
    echo "---\n";
}
