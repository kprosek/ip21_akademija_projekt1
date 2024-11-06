<?php
$currency = $argv[1] ?? null;
$pair = $argv[2] ?? null;
$apiUrl = 'https://api.coinbase.com/v2/prices/' . $currency . '-' . $pair . '/spot';

// Error handling
if ($currency === null || $pair === null) {
    echo sprintf('Error message: Missing arguments in the input');
    die;
}

if ($currency === 'help') {
    echo sprintf('Error message: Need to add arguments in the input - example: BTC USD');
    die;
}

if ((strlen($currency) < 3 && strlen($currency) > 10) || strlen($pair) !== 3) {
    echo sprintf('Error message: Wrong crypto or currency token length');
    die;
}

if (file_get_contents($apiUrl) === false) {
    echo sprintf('Error message: Wrong crypto or currency token');
    die;
};

// Valid data
$json = file_get_contents($apiUrl);

$displayData = json_decode($json, true);

echo sprintf('%s: %.2f %s', $displayData['data']['base'], $displayData['data']['amount'], $displayData['data']['currency']);
