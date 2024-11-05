<?php
$currency = $argv[1] ?? null;
$pair = $argv[2] ?? null;

if ($currency === null || $pair === null || $currency === 'help') {
    echo sprintf('Error: Wrong input');
    die;
}

$apiUrl = 'https://api.coinbase.com/v2/prices/' . $currency . '-' . $pair . '/spot';

$json = file_get_contents($apiUrl);

$displayData = json_decode($json, true);

echo sprintf('%s: %.2f %s', $displayData['data']['base'], $displayData['data']['amount'], $displayData['data']['currency']);
