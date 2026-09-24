<?php

namespace App\Scrapping;

use Symfony\Component\Panther\Client;
use Symfony\Component\Process\ExecutableFinder;

class ChromeClientFactory
{
    public static function create(array $arguments): Client
    {
        $_SERVER['PANTHER_CHROME_BINARY'] = getenv('PANTHER_CHROME_BINARY') ?: self::findChromeBinary();

        $driver = getenv('PANTHER_CHROME_DRIVER_BINARY');
        if (!$driver) {
            $bundled = dirname(__DIR__, 2).'/drivers/chromedriver';
            $driver = is_file($bundled) ? $bundled : null;
        }

        return Client::createChromeClient($driver, $arguments);
    }

    private static function findChromeBinary(): string
    {
        $finder = new ExecutableFinder();
        foreach (['google-chrome', 'google-chrome-stable'] as $name) {
            if ($binary = $finder->find($name)) {
                return $binary;
            }
        }

        return 'google-chrome';
    }
}
