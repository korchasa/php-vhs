<?php
declare(strict_types=1);

$file = __DIR__.'/README.md';
$content = file_get_contents(__DIR__.'/README.tpl.md');

preg_match_all('/>>>(.*?)<<</', $content, $matches);

foreach ($matches[1] as $fileName) {
    if (!file_exists($fileName)) {
        die("$fileName not found");
    }
    $language = pathinfo($fileName)['extension'];
    $content = str_replace(
        '>>>'.$fileName.'<<<',
        sprintf("```%s\n%s\n```", $language, file_get_contents($fileName)),
        $content
    );
}

if ($content) {
    file_put_contents($file, $content);
}
