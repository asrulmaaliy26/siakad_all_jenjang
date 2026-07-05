<?php
$count = 0;
\App\Models\SiswaDataLjk::chunk(100, function ($records) use (&$count) {
    foreach ($records as $record) {
        $record->updated_at = now();
        $record->save();
        $count++;
    }
});
echo "Updated $count records.\n";
