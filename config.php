<?php

require_once 'vendor/autoload.php';

use League\Config\Configuration;
use Nette\Schema\Expect;

function getAppConfig()
{
    $config = new Configuration([
        'api' => Expect::structure([
            'base_url' => Expect::string()->required()
        ]),
        'cli' => Expect::structure([
            'default_user_id' => Expect::int()
        ])
    ]);

    $defaultValues = [
        'api' => [
            'base_url' => 'https://api.coinbase.com/v2/'
        ],
        'cli' => ['default_user_id' => 0]
    ];

    $config->merge($defaultValues);

    return $config;
}
