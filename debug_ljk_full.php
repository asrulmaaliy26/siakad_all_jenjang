<?php

use Illuminate\Contracts\Console\Kernel;
use App\Models\SiswaDataLJK;
use Illuminate\Database\Eloquent\Builder;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

$studentName = 'Monroe Cole';

$records = SiswaDataLJK::whereHas('akademikKrs.riwayatPendidikan.siswaData', function ($query) use ($studentName) {
    $query->where('nama', 'like', "%{$studentName}%");
})->get();

echo "Found " . $records->count() . " records for $studentName.\n\n";

foreach ($records as $record) {
    echo "ID: " . $record->id . "\n";
    echo "Mata Pelajaran ID: " . $record->id_mata_pelajaran_kelas . "\n";
    echo "LJK UAS: " . ($record->ljk_uas ? $record->ljk_uas : 'NULL') . "\n";
    echo "CTT UAS (Raw): '" . $record->ctt_uas . "'\n";

    $stripped = strip_tags($record->ctt_uas ?? '');
    echo "CTT UAS (Strip Tags): '" . $stripped . "'\n";

    $hasImage = strpos($record->ctt_uas ?? '', '<img');
    echo "Has Image: " . ($hasImage !== false ? 'YES' : 'NO') . "\n";

    $condition = (!empty($record->ljk_uas) || (!empty($record->ctt_uas) && trim($stripped) !== ''));
    echo "Logic Result (current): " . ($condition ? 'SHOW' : 'HIDE') . "\n";

    // Check possible "empty" states
    echo "Is <p></p>? " . ($record->ctt_uas === '<p></p>' ? 'YES' : 'NO') . "\n";
    echo "Is <p><br></p>? " . ($record->ctt_uas === '<p><br></p>' ? 'YES' : 'NO') . "\n";
    echo "--------------------------------------------------\n";
}
