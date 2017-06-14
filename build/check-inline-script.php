<?php

// @codingStandardsIgnoreFile

require 'functions.php';

$err = false;
$errFn = isset($argv[1]) ? 'err' : 'printText';

try {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
} catch (UnexpectedValueException $e) {
    echo '忽略目录不存在的情况: ' . $e;
    exit;
}

$files = [];
foreach ($rii as $file) {
    if ($file->isDir()) {
        continue;
    }

    $doc = new DOMDocument();

    libxml_use_internal_errors(true);
    $doc->loadHTML(file_get_contents($file->getPathname()));
    libxml_use_internal_errors(false);

    $xpath = new DOMXpath($doc);
    /** @var DOMElement $node */
    foreach ($xpath->query('//script[string-length(text()) > 1]') as $node) {
        // 忽略模板
        if ($node->getAttribute('type') === 'text/html') {
            continue;
        }

        $count = substr_count($node->nodeValue, "\n");
        if ($count > 10) {
            $errFn('代码超过10行,需写到js文件中' . "\n"
                . '文件: ' . $file->getPathname() . "\n"
                . '首行: ' . explode("\n", trim($node->nodeValue))[0] . "\n");
        }
    }
}

echo '检查完毕,结果是' . ($err ? '不通过' : '通过') . "\n";

function printText($text)
{
    echo $text;
}
