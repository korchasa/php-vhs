<?php
declare(strict_types=1);

$output_file = __DIR__.'/README.md';
$output_content = file_get_contents(__DIR__.'/README.tpl.md');

preg_match_all('/>>>(.*?)<<</', $output_content, $matches);

foreach ($matches[1] as $match) {
    $output_content = str_replace('>>>'.$match.'<<<', file_get_contents($match), $output_content);
}

if ($output_content) {
    file_put_contents($output_file, $output_content);
}
