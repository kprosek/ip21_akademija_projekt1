<?php

class ConsoleView
{
    public function printList(array $list): void
    {
        foreach ($list as $key => $token) {
            echo $key . ' ' . $token . PHP_EOL;
        }
    }

    public function printPricePair(array $currencyPair): void
    {
        echo sprintf('%s: %.2f %s', $currencyPair['data']['base'], $currencyPair['data']['amount'], $currencyPair['data']['currency']);
    }

    public function printHelpText(string $text): void
    {
        echo $text;
    }

    public function printFavouriteTokens(string $text): void
    {
        echo PHP_EOL . "This are your favourite tokens: " . PHP_EOL . $text . PHP_EOL;
    }
}
