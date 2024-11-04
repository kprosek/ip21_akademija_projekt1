<?php
$currency = $argv[1];
$pair = $argv[2];
$apiUrl = 'https://api.coinbase.com/v2/prices/' . $currency . '-' . $pair . '/spot';

// Error handling
if (($currency === null || $pair === null) || $currency === 'help') {
    echo ('Help text');
    die;
} else if ((strlen($currency) !== 3 && strlen($currency) !== 4) || strlen($pair) !== 3) {
    echo ('Error: Wrong currency token');
    die;
}

if (file_get_contents($apiUrl) === false) {
    echo ('Error: Wrong currency pair');
    die;
};

// Valid data
$json = file_get_contents($apiUrl);

$displayData = json_decode($json, true);

echo sprintf('%s: %.2f %s', $displayData['data']['base'], $displayData['data']['amount'], $displayData['data']['currency']);
