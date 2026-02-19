<?php
$content = '<p>sadfasdf</p>';
$condition = (!empty($content) && $content !== '<p></p>');
echo "Content: '$content'\n";
echo "Condition result: " . ($condition ? 'TRUE' : 'FALSE') . "\n";

$content2 = '<p></p>';
$condition2 = (!empty($content2) && $content2 !== '<p></p>');
echo "Content2: '$content2'\n";
echo "Condition2 result: " . ($condition2 ? 'TRUE' : 'FALSE') . "\n";
