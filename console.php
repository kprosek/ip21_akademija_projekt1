<?php
$apiUrl = 'https://api.coinbase.com/v2/prices/BTC-USD/spot';

$json = file_get_contents($apiUrl);

$displayData = json_decode($json, true);

echo $displayData['data']['base'] . ': ';
echo $displayData['data']['amount'] . ' ';
echo $displayData['data']['currency'];
