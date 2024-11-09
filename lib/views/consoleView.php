<?php
// List of crypto tokens
function printList($list)
{
    $list = implode(', ', $list);
    echo sprintf($list);
}

// Output Price Pair
function printPricePair($currencyPair)
{
    if ($currencyPair === false) {
        echo sprintf('Error message: invalid or empty .json file');
    }

    echo sprintf('%s: %.2f %s', $currencyPair['data']['base'], $currencyPair['data']['amount'], $currencyPair['data']['currency']);
}

// Error messages
function printHelpText($api, $cryptoGet, $currencyGet, $crypto, $currencies)
{
    if ($cryptoGet === null || $currencyGet === null) {
        echo sprintf('Error message: Missing arguments in the input');
        die;
    }

    if ($cryptoGet === 'help') {
        echo sprintf('Error message: Need to add arguments in the input - example: BTC USD');
        die;
    }

    if ((strlen($cryptoGet) < 3 || strlen($cryptoGet) > 10) || strlen($currencyGet) !== 3) {
        echo sprintf('Error message: Wrong crypto or currency token length');
        die;
    }

    if (in_array($cryptoGet, $crypto) === false || in_array($currencyGet, $currencies) === false) {
        echo sprintf('Error message: Invalid crypto or currency token');
        die;
    }

    if (file_get_contents($api) === false) {
        echo sprintf('Error message: Unsupported token pair');
        die;
    };
}
