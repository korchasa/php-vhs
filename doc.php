<?php
declare(strict_types=1);

$file = __DIR__.'/README.md';
$content = file_get_contents(__DIR__.'/README.tpl.md');
$maxLength = 1500;

preg_match_all('/>>>(.*?)<<</', $content, $matches);

foreach ((array) $matches[1] as $fileName) {
    if (!file_exists($fileName)) {
        die("$fileName not found");
    }
    $language = pathinfo($fileName)['extension'];
    $replacement = file_get_contents($fileName);
    if (mb_strlen($replacement) > $maxLength) {
        $replacement = substr($replacement, 0, strpos($replacement, "\n", $maxLength))."\n...\n";
    }
    $content = str_replace(
        '>>>'.$fileName.'<<<',
        sprintf("```%s\n%s\n```", $language, $replacement),
        $content
    );
}

if ($content) {
    file_put_contents($file, $content);
}
