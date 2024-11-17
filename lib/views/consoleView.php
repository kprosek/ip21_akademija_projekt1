<?php

function printList($list)
{
    $list = implode(', ', $list);
    echo ($list);
}

function printPricePair($currencyPair)
{
    echo sprintf('%s: %.2f %s', $currencyPair['data']['base'], $currencyPair['data']['amount'], $currencyPair['data']['currency']);
}

function printHelpText($text)
{
    echo ('Error message: ' . $text);
}
