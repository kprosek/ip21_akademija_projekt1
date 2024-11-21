<?php

class ConsoleView
{
    public function printList(array $list): void
    {
        $list = implode(', ', $list);
        echo ($list);
    }

    public function printPricePair(array $currencyPair): void
    {
        echo sprintf('%s: %.2f %s', $currencyPair['data']['base'], $currencyPair['data']['amount'], $currencyPair['data']['currency']);
    }

    public function printHelpText(string $text): void
    {
        echo ('Error message: ' . $text);
    }
}
