<?php

function printList($list)
{
    $list = implode(', ', $list);
    echo ($list);
}

function printPricePair($currencyPair)
{
    if ($currencyPair === false) {
        echo ('Error message: invalid or empty .json file');
    }

    echo sprintf('%s: %.2f %s', $currencyPair['data']['base'], $currencyPair['data']['amount'], $currencyPair['data']['currency']);
}

function printHelpText($text)
{
    echo ('Error message: ' . $text);
}
