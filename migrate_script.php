#!/usr/bin/php
<?php

$directory = getcwd() . "\src\\";

function isUseOfCommonServiceStatement($line): bool
{
    return strpos($line, 'Admin\Library\Services') && !strpos($line, 'Admin\Library\Services\Commons');
}

function editFile($file): void
{
    $lines = file($file);
    foreach ($lines as $key => $line) {
        if (isUseOfCommonServiceStatement($line)) {
            $newLine = str_replace(
                'Admin\Library\Services\\',
                'Admin\Library\Services\Commons\\',
                $line
            );
            $lines[$key] = $newLine;
        }
        if (strpos($line, 'class')) {
            break;
        }
    }

    $fileWrite = fopen($file, 'wb');
    foreach ($lines as $line) {
        fwrite($fileWrite, $line);
    }

    fclose($fileWrite);
}

function modifyFiles($directory) {
    $dir = opendir($directory);
    while ($element = readdir($dir)){
        if ($element !== "." && $element !== ".."){
            $path = $directory.$element;
            if (
                is_dir($path) && !strpos($path, 'Entity') &&
                !strpos($path, 'Migrations') &&
                !strpos($path, 'Security') &&
                !strpos($path, 'Front') &&
                !strpos($path, 'Form') &&
                !strpos($path, 'Command')
            ){
                modifyFiles($path . '/');
            } elseif (strpos($path, '.php') && !strpos($path, 'Kernel')) {
                editFile($path);
            }
        }
    }
}

modifyFiles($directory);