<?php

const DATABASE_SOURCE = 'https://raw.githubusercontent.com/thm/uinames/master/names.json';
const DATABASE_DEST   = 'names.json';
const STATS_DEST      = 'stats.json';

// exit on any kind of error
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    echo $errstr . ' in ' . $errfile . ' on line ' . $errline;
    exit;
});

$file     = file_get_contents(DATABASE_SOURCE);
$database = json_decode($file);
$stats    = array(
    'names'     => 0,
    'regions' => 0
);

$regions = array_map(function ($pool) use (&$stats) {
    $stats['names'] += (count($pool->male) + count($pool->female)) * count($pool->surnames);
    return $pool->region;
}, $database);

$stats['regions'] = count(array_unique($regions));

$json       = json_encode($database);//, JSON_UNESCAPED_UNICODE);
$stats_json = json_encode((object) $stats);//, JSON_UNESCAPED_UNICODE);

file_put_contents(DATABASE_DEST, $json);
file_put_contents(STATS_DEST, $stats_json);

echo 'Database updated.';