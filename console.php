<?php
$currency = $argv[1];
$pair = $argv[2];

$apiUrl = 'https://api.coinbase.com/v2/prices/' . $currency . '-' . $pair . '/spot';

$json = file_get_contents($apiUrl);

$displayData = json_decode($json, true);

echo sprintf('%s: %.2f %s', $displayData['data']['base'], $displayData['data']['amount'], $displayData['data']['currency']);
