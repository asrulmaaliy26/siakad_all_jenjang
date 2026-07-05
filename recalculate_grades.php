<?php
$count = 0;
\App\Models\SiswaDataLjk::chunk(100, function ($records) use (&$count) {
    foreach ($records as $record) {
        if ((float)$record->Nilai_UTS > 0 || (float)$record->Nilai_UAS > 0 || (float)$record->Nilai_Akhir > 0) {
            $record->updated_at = now();
            $record->save();
            $count++;
        }
    }
});
echo "Updated $count records.\n";
