<?php

use Illuminate\Contracts\Console\Kernel;
use App\Models\SiswaDataLJK;
use Illuminate\Database\Eloquent\Builder;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

// Now we can use Eloquent
$studentName = 'Monroe Cole';

// We need to find the correct relationship path.
// Based on previous files, SiswaDataLJK -> akademikKrs -> riwayatPendidikan -> siswaData
// Let's verify if these relationships exist in the models.
// I'll assume they do based on the UjianRelationManager.php code:
// TextColumn::make('akademikKrs.riwayatPendidikan.siswaData.nama')

$record = SiswaDataLJK::whereHas('akademikKrs.riwayatPendidikan.siswaData', function ($query) use ($studentName) {
    $query->where('nama', 'like', "%{$studentName}%");
})->first();

if ($record) {
    echo "Student Found: " . $studentName . "\n";
    echo "ID: " . $record->id . "\n";
    echo "LJK UAS: " . ($record->ljk_uas ? $record->ljk_uas : 'NULL') . "\n";
    echo "CTT UAS (Raw): '" . $record->ctt_uas . "'\n";
    echo "CTT UAS Length: " . strlen($record->ctt_uas) . "\n";

    // Check for specific hidden characters using hex dump if needed, but simple trim/strip_tags is good first step.
    $stripped = strip_tags($record->ctt_uas);
    echo "CTT UAS (Strip Tags): '" . $stripped . "'\n";
    echo "CTT UAS (Strip Tags Length): " . strlen($stripped) . "\n";

    // Check specific condition used in the code
    $condition = (!empty($record->ctt_uas) && $record->ctt_uas !== '<p></p>');
    echo "Condition (!empty && !== <p></p>): " . ($condition ? 'TRUE' : 'FALSE') . "\n";

    if (!$condition) {
        echo "The condition is FALSE, meaning the system thinks it is empty or just <p></p>.\n";
        echo "Value comparison with '<p></p>': " . ($record->ctt_uas === '<p></p>' ? 'IDENTICAL' : 'NOT IDENTICAL') . "\n";
    }
} else {
    echo "Student '$studentName' not found or no SiswaDataLJK record found.\n";
    // Debug: output all students to see if name matches
    // $all = \App\Models\SiswaData::limit(5)->get();
    // foreach($all as $s) echo $s->nama . "\n";
}
