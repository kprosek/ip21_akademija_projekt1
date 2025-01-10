<?php

require_once('vendor/autoload.php');

class ConsoleView
{
    private $climate;

    public function __construct()
    {
        $this->climate = new League\CLImate\CLImate;
    }

    public function printList(array $list): void
    {
        foreach ($list as $key => $token) {
            $this->climate->out($key . ' ' . $token);
        }
    }

    public function printPricePair(array $currencyPair): void
    {
        $price = sprintf('%s: %.2f %s', $currencyPair['data']['base'], $currencyPair['data']['amount'], $currencyPair['data']['currency']);
        $this->climate->lightBlue()->out($price);
    }

    public function printHelpText(string $text): void
    {
        $this->climate->out($text);
    }

    public function printErrorText(string $text): void
    {
        $this->climate->red($text);
    }

    public function printSuccessText(string $text): void
    {
        $this->climate->lightGreen($text);
    }

    public function printFavouriteTokens(string $text): void
    {
        $this->climate->lightBlue(PHP_EOL . "Your favourite tokens: " . PHP_EOL . $text . PHP_EOL);
    }
}
